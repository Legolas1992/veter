<?php if(isset($_SESSION['usuario_id'])): ?>
            </div>
        </main>
    </div>
<?php endif; ?>

<footer style="margin-top: auto; padding: 1.5rem; background: #fff; text-align: center; border-top: 1px solid #eee; color: #666; font-size: 0.9rem;">
    <p>Sistema de Gestión Veterinaria &copy; <?php echo date('Y'); ?></p>
    <p style="font-weight: bold; color: var(--primary-dark); margin-top: 0.5rem;">
        Sistema creado por Llanes Manuel Alexandro - Técnico Programador
    </p>
</footer>

<script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>
</body>
</html>
