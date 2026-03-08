<?php
/**
 * Admin — Formulario Usuario (crear/editar)
 * Variables: $editUser (null=crear), $roles, $usuario (logueado), $csrf
 */
$isEdit = !empty($editUser);
$v = fn(string $key, string $default = '') => e($editUser[$key] ?? $default);
?>

<div class="admin-page-header">
    <h1><?= $isEdit ? 'Editar Usuario' : 'Nuevo Usuario' ?></h1>
    <p class="admin-page-subtitle">
        <a href="<?= url('/admin/usuarios') ?>">&larr; Volver al listado</a>
    </p>
</div>

<form method="POST"
      action="<?= $isEdit ? url("/admin/usuarios/{$editUser['id']}/editar") : url('/admin/usuarios/crear') ?>"
      class="admin-form" style="max-width: 560px;" id="formUsuario">
    <?= csrf_field() ?>

    <fieldset class="form-fieldset">
        <legend>Datos personales</legend>

        <div class="form-group">
            <label class="form-label" for="nombre">Nombre completo *</label>
            <input type="text" id="nombre" name="nombre" value="<?= $v('nombre') ?>"
                   class="form-input" required maxlength="100">
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?= $v('email') ?>"
                   class="form-input" required maxlength="200">
        </div>

        <div class="form-group">
            <label class="form-label" for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" value="<?= $v('telefono') ?>"
                   class="form-input" maxlength="20" placeholder="+56 9 ...">
        </div>
    </fieldset>

    <fieldset class="form-fieldset">
        <legend>Rol y estado</legend>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="rol_id">Rol *</label>
                <select id="rol_id" name="rol_id" class="form-select" required
                    <?= ($isEdit && (int)$editUser['id'] === (int)$usuario['id']) ? 'disabled' : '' ?>>
                    <?php foreach ($roles as $rol): ?>
                    <option value="<?= $rol['id'] ?>"
                        <?= ($isEdit && (int)$editUser['rol_id'] === (int)$rol['id']) ? 'selected' : '' ?>
                        <?= (!$isEdit && (int)$rol['id'] === 4) ? 'selected' : '' ?>>
                        <?= e($rol['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($isEdit && (int)$editUser['id'] === (int)$usuario['id']): ?>
                    <input type="hidden" name="rol_id" value="<?= $editUser['rol_id'] ?>">
                    <small class="text-muted">No puedes cambiar tu propio rol.</small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">&nbsp;</label>
                <label class="form-checkbox">
                    <input type="checkbox" name="activo" value="1"
                        <?= (!$isEdit || $editUser['activo']) ? 'checked' : '' ?>
                        <?= ($isEdit && (int)$editUser['id'] === (int)$usuario['id']) ? 'disabled' : '' ?>>
                    <span>Usuario activo</span>
                </label>
                <?php if ($isEdit && (int)$editUser['id'] === (int)$usuario['id']): ?>
                    <input type="hidden" name="activo" value="1">
                <?php endif; ?>
            </div>
        </div>
    </fieldset>

    <?php if (!$isEdit): ?>
    <fieldset class="form-fieldset">
        <legend>Contraseña</legend>

        <div class="form-group">
            <label class="form-label" for="password">Contraseña *</label>
            <div class="password-field">
                <input type="password" id="password" name="password" required
                       autocomplete="new-password" class="form-input" minlength="8">
                <button type="button" class="toggle-password" data-target="password" title="Mostrar/ocultar">&#128065;</button>
            </div>
            <div class="password-strength" id="passwordStrength"></div>
            <ul class="password-rules" id="passwordRules">
                <li id="rule-length">Mínimo 8 caracteres</li>
                <li id="rule-upper">Al menos 1 mayúscula</li>
                <li id="rule-lower">Al menos 1 minúscula</li>
                <li id="rule-number">Al menos 1 número</li>
            </ul>
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmar">Confirmar contraseña *</label>
            <div class="password-field">
                <input type="password" id="password_confirmar" name="password_confirmar" required
                       autocomplete="new-password" class="form-input">
                <button type="button" class="toggle-password" data-target="password_confirmar" title="Mostrar/ocultar">&#128065;</button>
            </div>
            <div class="password-match" id="passwordMatch"></div>
        </div>
    </fieldset>
    <?php else: ?>
    <fieldset class="form-fieldset">
        <legend>Contraseña</legend>
        <p class="text-muted" style="font-size: 14px;">
            La contraseña no se muestra por seguridad. El usuario puede cambiarla desde
            <a href="<?= url('/admin/cambiar-password') ?>">Cambiar contraseña</a>.
        </p>
    </fieldset>
    <?php endif; ?>

    <button type="submit" class="btn btn--primary"><?= $isEdit ? 'Guardar cambios' : 'Crear usuario' ?></button>
</form>

<?php if (!$isEdit): ?>
<style>
.password-field { position: relative; display: flex; align-items: center; }
.password-field .form-input { flex: 1; padding-right: 44px; }
.toggle-password { position: absolute; right: 8px; background: none; border: none; cursor: pointer; font-size: 18px; opacity: .6; padding: 4px; }
.toggle-password:hover { opacity: 1; }
.password-strength { height: 4px; border-radius: 2px; margin-top: 6px; background: var(--border); transition: background .2s; }
.password-strength.weak   { background: var(--admin-red); }
.password-strength.medium { background: var(--admin-yellow); }
.password-strength.strong { background: var(--admin-green-light); }
.password-rules { list-style: none; margin-top: 8px; font-size: 13px; color: var(--text-muted); }
.password-rules li { padding: 2px 0; }
.password-rules li::before { content: '\2716 '; color: var(--admin-red); }
.password-rules li.pass::before { content: '\2714 '; color: var(--admin-green-light); }
.password-rules li.pass { color: var(--text); }
.password-match { font-size: 13px; margin-top: 4px; min-height: 18px; }
.password-match.ok   { color: var(--admin-green-light); }
.password-match.fail { color: var(--admin-red); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var pw = document.getElementById('password');
    var confirm = document.getElementById('password_confirmar');
    var bar = document.getElementById('passwordStrength');
    var match = document.getElementById('passwordMatch');

    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.getElementById(this.getAttribute('data-target'));
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    pw.addEventListener('input', function() {
        var v = this.value;
        var hasLen = v.length >= 8, hasUpper = /[A-Z]/.test(v), hasLower = /[a-z]/.test(v), hasNum = /[0-9]/.test(v);
        document.getElementById('rule-length').className = hasLen ? 'pass' : '';
        document.getElementById('rule-upper').className = hasUpper ? 'pass' : '';
        document.getElementById('rule-lower').className = hasLower ? 'pass' : '';
        document.getElementById('rule-number').className = hasNum ? 'pass' : '';
        var score = (hasLen?1:0) + (hasUpper?1:0) + (hasLower?1:0) + (hasNum?1:0);
        bar.className = 'password-strength';
        if (v.length > 0) {
            if (score <= 2) bar.classList.add('weak');
            else if (score === 3) bar.classList.add('medium');
            else bar.classList.add('strong');
        }
        checkMatch();
    });

    confirm.addEventListener('input', checkMatch);

    function checkMatch() {
        if (!confirm.value.length) { match.textContent = ''; match.className = 'password-match'; return; }
        if (pw.value === confirm.value) { match.textContent = 'Las contraseñas coinciden'; match.className = 'password-match ok'; }
        else { match.textContent = 'Las contraseñas no coinciden'; match.className = 'password-match fail'; }
    }
});
</script>
<?php endif; ?>
