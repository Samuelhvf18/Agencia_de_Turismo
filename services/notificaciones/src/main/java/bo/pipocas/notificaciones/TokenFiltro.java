package bo.pipocas.notificaciones;

import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Component;
import org.springframework.web.filter.OncePerRequestFilter;

@Component
class TokenFiltro extends OncePerRequestFilter {
    private final byte[] esperado;

    TokenFiltro(@Value("${pipocas.token}") String token) {
        if (token.length() < 32) throw new IllegalArgumentException("NOTIFICACIONES_TOKEN debe tener al menos 32 caracteres.");
        esperado = ("Bearer " + token).getBytes(StandardCharsets.UTF_8);
    }

    @Override
    protected void doFilterInternal(HttpServletRequest request, HttpServletResponse response, FilterChain chain)
            throws ServletException, IOException {
        String recibido = request.getHeader("Authorization");
        if (recibido == null || !MessageDigest.isEqual(esperado, recibido.getBytes(StandardCharsets.UTF_8))) {
            response.sendError(401);
            return;
        }
        chain.doFilter(request, response);
    }
}
