<div class="card">

    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <div class="form-grid">

        <div class="form-group">

            <label>

                Lookup Type

            </label>

            <input
                type="text"
                value="<?= htmlspecialchars($lookupType['name']) ?>"
                readonly
            >

        </div>

        <div class="form-group">

            <label>

                Name <span class="required">*</span>

            </label>

            <input
                type="text"
                name="name"
                required
                value="<?= htmlspecialchars(
                    $lookupValue['name']
                    ?? $old['name']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Slug

            </label>

            <input
                type="text"
                name="slug"
                value="<?= htmlspecialchars(
                    $lookupValue['slug']
                    ?? $old['slug']
                    ?? ''
                ) ?>"
            >

            <small>

                Leave blank to auto generate.

            </small>

        </div>

        <div class="form-group">

            <label>

                Display Order

            </label>

            <input
                type="number"
                name="display_order"
                value="<?= htmlspecialchars(
                    $lookupValue['display_order']
                    ?? $old['display_order']
                    ?? 0
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Icon

            </label>

            <input
                type="text"
                name="icon"
                value="<?= htmlspecialchars(
                    $lookupValue['icon']
                    ?? $old['icon']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Color

            </label>

            <input
                type="text"
                name="color"
                placeholder="#2563eb"
                value="<?= htmlspecialchars(
                    $lookupValue['color']
                    ?? $old['color']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Status

            </label>

            <?php

            $status =
                $lookupValue['status']
                ?? $old['status']
                ?? 'Active';

            ?>

            <select name="status">

                <option
                    value="Active"
                    <?= $status === 'Active'
                        ? 'selected'
                        : '' ?>
                >

                    Active

                </option>

                <option
                    value="Inactive"
                    <?= $status === 'Inactive'
                        ? 'selected'
                        : '' ?>
                >

                    Inactive

                </option>

            </select>

        </div>

        <div class="form-group full-width">

            <label>

                Description

            </label>

            <textarea
                name="description"
                rows="4"
            ><?= htmlspecialchars(
                $lookupValue['description']
                ?? $old['description']
                ?? ''
            ) ?></textarea>

        </div>

    </div>

    <div class="page-actions">

        <button
            class="btn btn-primary"
            type="submit"
        >

            Save Lookup Value

        </button>

        <a
            href="<?= config('app.base_path') ?>/lookup-types/<?= $lookupType['id'] ?>"
            class="btn btn-outline"
        >

            Cancel

        </a>

    </div>

</div>