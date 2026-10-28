<input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">

<div class="campos">
    <label>Cédula *
        <input type="text" name="cedula"
               pattern="[0-9]{10}" maxlength="10"
               inputmode="numeric" required
               value="<?= e($datos["cedula"] ?? "") ?>">
    </label>

    <label>Nombres *
        <input type="text" name="nombres" maxlength="100" required
               value="<?= e($datos["nombres"] ?? "") ?>">
    </label>

    <label>Apellidos *
        <input type="text" name="apellidos" maxlength="100" required
               value="<?= e($datos["apellidos"] ?? "") ?>">
    </label>

    <label>Fecha de nacimiento *
        <input type="date" name="fecha_nacimiento" required
               max="<?= date("Y-m-d") ?>"
               value="<?= e($datos["fecha_nacimiento"] ?? "") ?>">
    </label>

    <label>Teléfono
        <input type="tel" name="telefono" maxlength="20"
               value="<?= e($datos["telefono"] ?? "") ?>">
    </label>

    <label>Tipo de sangre
        <select name="tipo_sangre">
            <?php foreach (tiposSangre() as $tipo): ?>
                <option value="<?= e($tipo) ?>"
                    <?= ($datos["tipo_sangre"] ?? "Desconocido") === $tipo
                        ? "selected" : "" ?>>
                    <?= e($tipo) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Dirección
        <input type="text" name="direccion" maxlength="200"
               value="<?= e($datos["direccion"] ?? "") ?>">
    </label>

    <label>Alergias
        <input type="text" name="alergias" maxlength="255"
               placeholder="No informado / Sin alergias conocidas / Detalle"
               value="<?= e($datos["alergias"] ?? "") ?>">
    </label>

    <label>Motivo de ingreso *
        <textarea name="motivo_ingreso" maxlength="255"
                  required><?= e($datos["motivo_ingreso"] ?? "") ?></textarea>
    </label>
</div>

<p>* Campos obligatorios</p>
<button type="submit">Guardar paciente</button>