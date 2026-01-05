from django.db import models

class Cliente(models.Model):
    nombre = models.CharField(max_length=100)
    telefono = models.CharField(max_length=15)
    email = models.EmailField(unique=True)
    direccion = models.CharField(max_length=200)

    def __str__(self):
        return self.nombre

class Paciente(models.Model):
    SEXO_CHOICES = [
        ('M', 'Macho'),
        ('H', 'Hembra'),
    ]

    nombre = models.CharField(max_length=100)
    especie = models.CharField(max_length=50)
    raza = models.CharField(max_length=50)
    edad = models.PositiveIntegerField()
    peso = models.FloatField()
    sexo = models.CharField(max_length=1, choices=SEXO_CHOICES)
    dueño = models.ForeignKey(Cliente, on_delete=models.CASCADE, related_name='pacientes')

    def __str__(self):
        return f"{self.nombre} ({self.especie})"
