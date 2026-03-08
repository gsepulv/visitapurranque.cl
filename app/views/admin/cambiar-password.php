<?php
/**
 * Admin — Cambiar contraseña
 * Variables: $usuario, $csrf, $flash
 */
?>

<div class="admin-page-header">
    <h1>Cambiar contraseña</h1>
</div>

<div class="admin-card" style="max-width: 520px;">
    <form method="POST" action="<?= url('/admin/cambiar-password') ?>" autocomplete="off" id="formPassword">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

        <div class="form-group">
            <label for="password_actual">Contraseña actual</label>
            <div class="password-field">
                <input type="password" id="password_actual" name="password_actual"
                       required autocomplete="current-password" class="form-input">
                <button type="button" class="toggle-password" data-target="password_actual" title="Mostrar/ocultar">&#128065;</button>
            </div>
        </div>

        <div class="form-group">
            <label for="password_nueva">Nueva contraseña</label>
            <div class="password-field">
                <input type="password" id="password_nueva" name="password_nueva"
                       required autocomplete="new-password" class="form-input">
                <button type="button" class="toggle-password" data-target="password_nueva" title="Mostrar/ocultar">&#128065;</button>
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
            <label for="password_confirmar">Confirmar nueva contraseña</label>
            <div class="password-field">
                <input type="password" id="password_confirmar" name="password_confirmar"
                       required autocomplete="new-password" class="form-input">
                <button type="button" class="toggle-password" data-target="password_confirmar" title="Mostrar/ocultar">&#128065;</button>
            </div>
            <div class="password-match" id="passwordMatch"></div>
        </div>

        <button type="submit" class="btn btn-primary" id="btnSubmit">Actualizar contraseña</button>
    </form>
</div>

<style>
.password-field {
    position: relative;
    display: flex;
    align-items: center;
}
.password-field .form-input {
    flex: 1;
    padding-right: 44px;
}
.toggle-password {
    position: absolute;
    right: 8px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 18px;
    opacity: .6;
    padding: 4px;
}
.toggle-password:hover {
    opacity: 1;
}
.password-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 6px;
    background: var(--border);
    transition: background .2s;
}
.password-strength.weak   { background: var(--admin-red); }
.password-strength.medium { background: var(--admin-yellow); }
.password-strength.strong { background: var(--admin-green-light); }

.password-rules {
    list-style: none;
    margin-top: 8px;
    font-size: 13px;
    color: var(--text-muted);
}
.password-rules li {
    padding: 2px 0;
}
.password-rules li::before {
    content: '\2716 ';
    color: var(--admin-red);
}
.password-rules li.pass::before {
    content: '\2714 ';
    color: var(--admin-green-light);
}
.password-rules li.pass {
    color: var(--text);
}

.password-match {
    font-size: 13px;
    margin-top: 4px;
    min-height: 18px;
}
.password-match.ok   { color: var(--admin-green-light); }
.password-match.fail { color: var(--admin-red); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var pw = document.getElementById('password_nueva');
    var confirm = document.getElementById('password_confirmar');
    var bar = document.getElementById('passwordStrength');
    var match = document.getElementById('passwordMatch');

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var input = document.getElementById(this.getAttribute('data-target'));
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });

    pw.addEventListener('input', function() {
        var v = this.value;
        var hasLen = v.length >= 8;
        var hasUpper = /[A-Z]/.test(v);
        var hasLower = /[a-z]/.test(v);
        var hasNum = /[0-9]/.test(v);

        document.getElementById('rule-length').className = hasLen ? 'pass' : '';
        document.getElementById('rule-upper').className = hasUpper ? 'pass' : '';
        document.getElementById('rule-lower').className = hasLower ? 'pass' : '';
        document.getElementById('rule-number').className = hasNum ? 'pass' : '';

        var score = (hasLen ? 1 : 0) + (hasUpper ? 1 : 0) + (hasLower ? 1 : 0) + (hasNum ? 1 : 0);
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
        if (confirm.value.length === 0) {
            match.textContent = '';
            match.className = 'password-match';
            return;
        }
        if (pw.value === confirm.value) {
            match.textContent = 'Las contraseñas coinciden';
            match.className = 'password-match ok';
        } else {
            match.textContent = 'Las contraseñas no coinciden';
            match.className = 'password-match fail';
        }
    }
});
</script>
