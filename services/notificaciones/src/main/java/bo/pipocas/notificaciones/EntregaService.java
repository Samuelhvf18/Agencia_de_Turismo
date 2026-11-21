package bo.pipocas.notificaciones;

import java.nio.charset.StandardCharsets;
import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import java.security.GeneralSecurityException;
import java.util.HexFormat;
import java.util.Base64;
import java.util.ArrayList;
import java.util.List;
import org.springframework.core.io.ByteArrayResource;
import org.springframework.mail.MailPreparationException;
import org.springframework.mail.javamail.MimeMessageHelper;
import jakarta.mail.MessagingException;
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
        List<byte[]> archivos = new ArrayList<>();
        StringBuilder contenido = new StringBuilder(mensaje.correo()).append('\n').append(mensaje.asunto()).append('\n').append(mensaje.texto());
        int total = 0;
        for (var adjunto : mensaje.adjuntos()) {
            byte[] bytes = Base64.getDecoder().decode(adjunto.contenido());
            total += bytes.length;
            if (bytes.length > 5_000_000 || total > 10_000_000 || bytes.length < 5
                    || !new String(bytes, 0, 5, StandardCharsets.US_ASCII).equals("%PDF-")) {
                throw new IllegalArgumentException("Adjunto PDF inválido o demasiado grande.");
            }
            archivos.add(bytes);
            contenido.append('\n').append(adjunto.nombre()).append(':').append(adjunto.contenido());
        }
        String huella = huella(contenido.toString());
        // Persiste solo recibos de entrega.
        db.update("INSERT INTO entregas (clave, huella) SELECT ?, ? WHERE NOT EXISTS (SELECT 1 FROM entregas WHERE clave = ?)", clave, huella, clave);
        var recibo = db.queryForMap("SELECT huella, enviada_en FROM entregas WHERE clave = ? FOR UPDATE", clave);
        if (!huella.equals(recibo.get("HUELLA"))) throw new IllegalArgumentException("Clave reutilizada para otro mensaje.");
        if (recibo.get("ENVIADA_EN") != null) return;
        if (archivos.isEmpty()) {
            var correo = new SimpleMailMessage();
            correo.setFrom(from);
            correo.setTo(mensaje.correo());
            correo.setSubject(mensaje.asunto());
            correo.setText(mensaje.texto());
            mail.send(correo);
        } else {
            try {
                var correo = mail.createMimeMessage();
                var helper = new MimeMessageHelper(correo, true, StandardCharsets.UTF_8.name());
                helper.setFrom(from);
                helper.setTo(mensaje.correo());
                helper.setSubject(mensaje.asunto());
                helper.setText(mensaje.texto());
                for (int i = 0; i < archivos.size(); i++) {
                    helper.addAttachment(mensaje.adjuntos().get(i).nombre(), new ByteArrayResource(archivos.get(i)), "application/pdf");
                }
                mail.send(correo);
            } catch (MessagingException e) {
                throw new MailPreparationException(e);
            }
        }
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
