FROM maven:3.9-eclipse-temurin-21 AS build
WORKDIR /app
COPY pom.xml .
COPY src ./src
RUN mvn -B package
FROM eclipse-temurin:21-jre
RUN useradd --system --create-home pipocas
WORKDIR /app
COPY --from=build /app/target/notificaciones-1.0.0.jar app.jar
RUN mkdir data && chown -R pipocas:pipocas /app
USER pipocas
ENV BIND_ADDRESS=0.0.0.0
EXPOSE 8081
ENTRYPOINT ["java", "-jar", "app.jar"]
