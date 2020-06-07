<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th width="35%"><?= e(lang('igniter.api::default.text_allow_only')) ?></th>
            <th></th>
            <th class="list-action"><?= e(lang('admin::lang.text_disabled')) ?>/<?= e(lang('admin::lang.text_enabled')) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $fieldValue = !is_array($field->value) ? [$field->value] : $field->value;
        foreach ($field->options() as $action => $name) { ?>
        <tr>
            <td>
                <select
                    id="<?= $field->getId($action.'-authorization') ?>"
                    name="<?= $field->getName() ?>[authorization][<?= $action ?>]"
                    class="form-control"
                >
                    <?php foreach ($field->getConfig('authOptions') as $key => $label) { ?>
                        <option
                            value="<?= $key ?>"
                            <?= $key == array_get($fieldValue, 'authorization.'.$action) ? 'selected="selected"' : '' ?>
                        ><?= e(lang($label)) ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><?= e(lang($name)) ?></td>
            <td class="list-action text-right">
                <div class="field-custom-container">
                    <div class="custom-control custom-switch">
                        <input
                            type="checkbox"
                            class="custom-control-input"
                            id="<?= $field->getId($action.'-actions') ?>"
                            name="<?= $field->getName() ?>[actions][]"
                            value="<?= $action ?>"
                            <?= $this->previewMode ? 'disabled="disabled"' : '' ?>
                            <?= in_array($action, array_get($fieldValue, 'actions', [$action])) ? 'checked="checked"' : '' ?>
                        />
                        <label
                            class="custom-control-label"
                            for="<?= $field->getId($action.'-actions') ?>"
                        >&nbsp;</label>
                    </div>
                </div>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
</div>