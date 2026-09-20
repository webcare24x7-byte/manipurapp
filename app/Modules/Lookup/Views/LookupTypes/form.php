<div class="card">

    <?php if (!empty($error)): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <div class="form-grid">

        <div class="form-group">

            <label>

                Name <span class="required">*</span>

            </label>

            <input
                type="text"
                name="name"
                required
                value="<?= htmlspecialchars(
                    $lookupType['name']
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
                    $lookupType['slug']
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

                Icon

            </label>

            <input
                type="text"
                name="icon"
                value="<?= htmlspecialchars(
                    $lookupType['icon']
                    ?? $old['icon']
                    ?? ''
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Display Order

            </label>

            <input
                type="number"
                name="display_order"
                value="<?= htmlspecialchars(
                    $lookupType['display_order']
                    ?? $old['display_order']
                    ?? 0
                ) ?>"
            >

        </div>

        <div class="form-group">

            <label>

                Status

            </label>

            <?php

            $status =
                $lookupType['status']
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
                rows="5"
            ><?= htmlspecialchars(
                $lookupType['description']
                ?? $old['description']
                ?? ''
            ) ?></textarea>

        </div>

    </div>

    <div class="page-actions">

        <button
            type="submit"
            class="btn btn-primary"
        >

            Save Lookup Type

        </button>

        <a
            href="<?= config('app.base_path') ?>/lookup-types"
            class="btn btn-outline"
        >

            Cancel

        </a>

    </div>

</div>