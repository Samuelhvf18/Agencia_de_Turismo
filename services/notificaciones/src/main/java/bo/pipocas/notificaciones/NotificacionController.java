package bo.pipocas.notificaciones;

import jakarta.validation.Valid;
import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Pattern;
import jakarta.validation.constraints.Size;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.annotation.Validated;
import org.springframework.web.bind.annotation.*;

@RestController
@Validated
class NotificacionController {
    record Mensaje(
        @NotBlank @Email @Size(max = 150) String correo,
        @NotBlank @Size(max = 150) @Pattern(regexp = "[^\\r\\n]+") String asunto,
        @NotBlank @Size(max = 12000) String texto
    ) {}

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
