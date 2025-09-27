<h1 class="h4 mb-3">Admin Settings</h1>
<form method="post" action="/admin/settings">
  <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(App\Core\Csrf::token(), ENT_QUOTES, 'UTF-8'); ?>">
  <div class="row g-3">
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h2 class="h6">SAM API</h2>
          <div class="mb-3">
            <label class="form-label">API Key</label>
            <input class="form-control" type="text" name="sam_api_key" value="<?php echo htmlspecialchars($settings['sam_api_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
            <div class="form-text">Stored encrypted at rest in future; masked in UI as needed.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Environment</label>
            <select class="form-select" name="sam_environment">
              <?php $env = $settings['sam_environment'] ?? 'prod'; ?>
              <option value="prod" <?php echo $env==='prod'?'selected':''; ?>>prod</option>
              <option value="alpha" <?php echo $env==='alpha'?'selected':''; ?>>alpha</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <h2 class="h6">Default Filters</h2>
          <?php $defaults = json_decode($settings['default_filters'] ?? '{}', true) ?: []; ?>
          <div class="mb-3">
            <label class="form-label">Posted window (days ≤ 365)</label>
            <input class="form-control" type="number" name="posted_window_days" min="1" max="365" value="<?php echo (int)($defaults['posted_window_days'] ?? 30); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">NAICS (comma-separated)</label>
            <input class="form-control" type="text" name="naics" value="<?php echo htmlspecialchars(implode(',', $defaults['naics'] ?? ['334111','541512']), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Set-Aside (comma-separated, e.g., WOSB,EDWOSB)</label>
            <input class="form-control" type="text" name="set_aside" value="<?php echo htmlspecialchars(implode(',', $defaults['set_aside'] ?? []), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="form-check">
            <?php $onlyOpen = !empty($defaults['only_open']); ?>
            <input class="form-check-input" type="checkbox" id="only_open" name="only_open" <?php echo $onlyOpen? 'checked':''; ?>>
            <label class="form-check-label" for="only_open">Only open opportunities</label>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Save</button>
  </div>
</form>
