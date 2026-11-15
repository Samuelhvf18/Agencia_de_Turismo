package bo.pipocas.notificaciones;

import java.nio.charset.StandardCharsets;
import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import java.security.GeneralSecurityException;
import java.util.HexFormat;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.mail.SimpleMailMessage;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
class EntregaService {
    private final JavaMailSender mail;
    private final JdbcTemplate db;
    private final String from;
    private final byte[] secret;

    EntregaService(JavaMailSender mail, JdbcTemplate db, @Value("${pipocas.from}") String from, @Value("${pipocas.token}") String token) {
        this.mail = mail;
        this.db = db;
        this.from = from;
        this.secret = token.getBytes(StandardCharsets.UTF_8);
    }

    @Transactional
    public void enviar(String clave, NotificacionController.Mensaje mensaje) {
        String huella = huella(mensaje.correo() + "\n" + mensaje.asunto() + "\n" + mensaje.texto());
        // Solo recibos: no persistir códigos OTP, destinatarios ni el contenido del correo.
        db.update("INSERT INTO entregas (clave, huella) SELECT ?, ? WHERE NOT EXISTS (SELECT 1 FROM entregas WHERE clave = ?)", clave, huella, clave);
        var recibo = db.queryForMap("SELECT huella, enviada_en FROM entregas WHERE clave = ? FOR UPDATE", clave);
        if (!huella.equals(recibo.get("HUELLA"))) throw new IllegalArgumentException("Clave reutilizada para otro mensaje.");
        if (recibo.get("ENVIADA_EN") != null) return;
        var correo = new SimpleMailMessage();
        correo.setFrom(from);
        correo.setTo(mensaje.correo());
        correo.setSubject(mensaje.asunto());
        correo.setText(mensaje.texto());
        mail.send(correo);
        db.update("UPDATE entregas SET enviada_en = CURRENT_TIMESTAMP WHERE clave = ?", clave);
    }

    private String huella(String value) {
        try {
            Mac mac = Mac.getInstance("HmacSHA256");
            mac.init(new SecretKeySpec(secret, "HmacSHA256"));
            return HexFormat.of().formatHex(mac.doFinal(value.getBytes(StandardCharsets.UTF_8)));
        } catch (GeneralSecurityException e) {
            throw new IllegalStateException(e);
        }
    }
}
