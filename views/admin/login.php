<section class="login">
    <h1>Entrar al panel</h1>
    <p class="subtitulo">Escribe la contraseña del administrador.</p>
    <form method="post" action="<?= e(url('admin/login')) ?>" class="formulario">
        <?= csrf_campo() ?>
        <label>Contraseña
            <input type="password" name="clave" required autofocus>
        </label>
        <button class="btn btn-rosa btn-bloque" type="submit">Entrar</button>
    </form>
</section>
