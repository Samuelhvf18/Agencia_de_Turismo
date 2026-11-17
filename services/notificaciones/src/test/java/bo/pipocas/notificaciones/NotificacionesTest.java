package bo.pipocas.notificaciones;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.post;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.status;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.mail.MailSendException;
import org.springframework.mail.SimpleMailMessage;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;
import org.mockito.ArgumentCaptor;
import jakarta.mail.Session;
import jakarta.mail.internet.MimeMessage;
import java.util.Base64;
import java.util.Properties;
import static org.junit.jupiter.api.Assertions.*;

@SpringBootTest(properties = {"pipocas.token=token-de-prueba-de-al-menos-32-caracteres", "spring.datasource.url=jdbc:h2:mem:tests;DB_CLOSE_DELAY=-1"})
@AutoConfigureMockMvc
class NotificacionesTest {
    @Autowired MockMvc mvc;
    @Autowired JdbcTemplate db;
    @MockitoBean JavaMailSender mail;
    private static final String TOKEN = "Bearer token-de-prueba-de-al-menos-32-caracteres";
    private static final String BODY = "{\"correo\":\"test@example.com\",\"asunto\":\"Código\",\"texto\":\"Código de prueba\"}";

    @BeforeEach void limpiar() { db.update("DELETE FROM entregas"); reset(mail); }
    @Test void exigeAutenticacion() throws Exception {
        mvc.perform(post("/api/notificaciones").contentType("application/json").content(BODY)).andExpect(status().isUnauthorized());
        verifyNoInteractions(mail);
    }
    @Test void deduplicaInclusoEntreSolicitudes() throws Exception {
        for (int i = 0; i < 2; i++) {
            mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY)).andExpect(status().isNoContent());
        }
        verify(mail, times(1)).send(any(SimpleMailMessage.class));
    }
    @Test void noAceptaReutilizarClaveConOtroContenido() throws Exception {
        mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY)).andExpect(status().isNoContent());
        mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY.replace("Código de prueba", "Otro contenido"))).andExpect(status().isBadRequest());
        verify(mail, times(1)).send(any(SimpleMailMessage.class));
    }
    @Test void fallaSmtpPermiteReintento() throws Exception {
        doThrow(new MailSendException("prueba")).doNothing().when(mail).send(any(SimpleMailMessage.class));
        mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY)).andExpect(status().isServiceUnavailable());
        mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY)).andExpect(status().isNoContent());
    }
    @Test void rechazaCorreoInvalido() throws Exception {
        mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "evento:1").contentType("application/json").content(BODY.replace("test@example.com", "invalido"))).andExpect(status().isBadRequest());
        verifyNoInteractions(mail);
    }

    @Test void enviaTicketPdfYDeduplica() throws Exception {
        when(mail.createMimeMessage()).thenAnswer(invocation -> new MimeMessage(Session.getInstance(new Properties())));
        String pdf = Base64.getEncoder().encodeToString("%PDF-1.4\nprueba".getBytes(java.nio.charset.StandardCharsets.UTF_8));
        String body = BODY.substring(0, BODY.length() - 1) + ",\"adjuntos\":[{\"nombre\":\"ticket-RES-1.pdf\",\"contenido\":\"" + pdf + "\"}]}";
        for (int i = 0; i < 2; i++) {
            mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "ticket:1")
                .contentType("application/json").content(body)).andExpect(status().isNoContent());
        }
        var captor = ArgumentCaptor.forClass(MimeMessage.class);
        verify(mail, times(1)).send(captor.capture());
        captor.getValue().saveChanges();
        var out = new java.io.ByteArrayOutputStream();
        captor.getValue().writeTo(out);
        assertTrue(out.toString().contains("ticket-RES-1.pdf"));
        assertTrue(out.toString().contains("application/pdf"));
    }

    @Test void rechazaBase64YPdfInvalidos() throws Exception {
        for (String contenido : new String[]{"???", "aG9sYQ=="}) {
            String body = BODY.substring(0, BODY.length() - 1) + ",\"adjuntos\":[{\"nombre\":\"ticket.pdf\",\"contenido\":\"" + contenido + "\"}]}";
            mvc.perform(post("/api/notificaciones").header("Authorization", TOKEN).header("Idempotency-Key", "ticket:mal")
                .contentType("application/json").content(body)).andExpect(status().isBadRequest());
        }
        verifyNoInteractions(mail);
    }
}
