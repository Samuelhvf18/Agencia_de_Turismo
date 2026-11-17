package bo.pipocas.notificaciones;

import jakarta.validation.Valid;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import jakarta.validation.constraints.Size;
import jakarta.validation.constraints.NotNull;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.annotation.Validated;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@Validated
class NotificacionController {
    record Adjunto(
        @NotBlank @Size(max = 100) @Pattern(regexp = "[A-Za-z0-9._-]+\\.pdf") String nombre,
        @NotBlank @Size(max = 7000000) String contenido
    ) {}

    record Mensaje(
        @NotBlank @Email @Size(max = 150) String correo,
        @NotBlank @Size(max = 150) @Pattern(regexp = "[^\\r\\n]+") String asunto,
        @NotBlank @Size(max = 12000) String texto,
        @Size(max = 3) List<@NotNull @Valid Adjunto> adjuntos
    ) {
        Mensaje {
            adjuntos = adjuntos == null ? List.of() : adjuntos;
        }
    }

    private final EntregaService entregas;
    NotificacionController(EntregaService entregas) { this.entregas = entregas; }

    @PostMapping("/api/notificaciones")
    ResponseEntity<Void> enviar(
        @RequestHeader("Idempotency-Key") @Pattern(regexp = "[A-Za-z0-9:._-]{1,150}") String clave,
        @Valid @RequestBody Mensaje mensaje
    ) {
        entregas.enviar(clave, mensaje);
        return ResponseEntity.noContent().build();
    }

    @ExceptionHandler({IllegalArgumentException.class, jakarta.validation.ConstraintViolationException.class})
    ResponseEntity<Void> invalido() { return ResponseEntity.badRequest().build(); }

    @ExceptionHandler({org.springframework.mail.MailException.class, org.springframework.dao.DataAccessException.class})
    ResponseEntity<Void> temporal() { return ResponseEntity.status(503).build(); }
}
