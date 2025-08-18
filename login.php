<?php
// login.php - Responsive Login/Signup page
?>
<?php include __DIR__ . '/includes/header.php'; ?>

<section class="py-4 py-md-5">
  <div class="container">
    <div class="row g-4 justify-content-center align-items-stretch">
      <!-- Left: Benefits / Visual -->
      <div class="col-lg-5 d-none d-lg-block">
        <div class="h-100 rounded-4 overflow-hidden position-relative" style="background:url('assets/img/stay-in-puri.png') center/contain no-repeat, linear-gradient(135deg,#0d6efd33,#20c99733); min-height:560px;">
          
        </div>
      </div>

      <!-- Right: Login Card -->
      <div class="col-12 col-lg-6">
        <div class="card border-0 rounded-4 auth-card">
          <div class="card-body p-3 p-sm-4 p-md-5">

            <!-- Tabs -->
            <div class="d-flex bg-light rounded-pill p-1 mb-4 gap-1" role="tablist">
              <button class="btn flex-fill rounded-pill active" id="tab-login" data-target="#panel-login" type="button">Login</button>
              <button class="btn flex-fill rounded-pill" id="tab-register" data-target="#panel-register" type="button">Register</button>
              <button class="btn flex-fill rounded-pill" id="tab-business" data-target="#panel-business" type="button">Add Business</button>
            </div>

            <!-- Panels -->
            <div id="panel-login" class="tab-panel show">
              <label class="form-label">Mobile Number</label>
              <div class="input-group mb-3">
                <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control" id="mobileNumber" placeholder="Enter mobile number" maxlength="15">
              </div>
              <button id="continueBtn" class="btn btn-primary w-100 rounded-pill mb-3" type="button" disabled>Continue</button>
              <p class="small text-muted mt-4 mb-0">By proceeding, you agree to StayInPuri's <a href="#" class="link-secondary">Privacy Policy</a>, <a href="#" class="link-secondary">User Agreement</a> and <a href="#" class="link-secondary">T&amp;Cs</a>.</p>
            </div>

            <div id="panel-register" class="tab-panel" hidden>
              <div class="mb-3">
                <label for="regName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="regName" placeholder="Enter full name">
              </div>
              <div class="mb-3">
                <label for="regEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="regEmail" placeholder="name@example.com">
              </div>
              <div class="mb-3">
                <label for="regMobile" class="form-label">Mobile Number</label>
                <div class="input-group">
                  <input type="tel" inputmode="numeric" pattern="[0-9]*" class="form-control" id="regMobile" placeholder="Enter mobile number" maxlength="15">
                </div>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="regTerms">
                <label class="form-check-label" for="regTerms">
                  I agree to the Terms & Conditions
                </label>
              </div>
              <button id="registerBtn" class="btn btn-primary w-100 rounded-pill mb-3" type="button" disabled>Create Account</button>
            </div>

            <div id="panel-business" class="tab-panel" hidden>
              <div class="mb-3">
                <label class="form-label">Choose a listing type</label>
                <div class="row g-2 g-sm-3 business-options">
                  <div class="col-12 col-md-6">
                    <a href="#" class="option w-100" title="Add your hotel">
                      <span class="icon"><i class="fa-solid fa-hotel"></i></span>
                      <span class="label">Hotel Listing</span>
                    </a>
                  </div>
                  <div class="col-12 col-md-6">
                    <a href="#" class="option w-100" title="Add your restaurant">
                      <span class="icon"><i class="fa-solid fa-utensils"></i></span>
                      <span class="label">Restaurant Listing</span>
                    </a>
                  </div>
                  <div class="col-12">
                    <a href="#" class="option w-100" title="Add your tour">
                      <span class="icon"><i class="fa-solid fa-route"></i></span>
                      <span class="label">Tour Listing</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  /* Page specific styles */
  .tab-panel { transition: opacity .2s ease; }
  .tab-panel.show { opacity: 1; }
  .tab-panel[hidden] { opacity: 0; }
  .auth-card [role="tablist"] .btn { background: transparent; color: var(--primary-color); }
  .auth-card [role="tablist"] .btn.active { background: var(--primary-color); color: #fff; }
  @media (max-width: 991.98px) {
    /* Stack nicely on mobile */
  }
</style>
 
<style>
  /* Auth card – clean, modern form styling (scoped) */
  .auth-card { background: #fff; }
  .auth-card .form-label {
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: .35rem;
  }
  .auth-card .form-control {
    height: 48px;
    border-radius: 10px;
    border: 1px solid var(--border-color);
    background: #fff;
    box-shadow: none;
  }
  .auth-card .input-group > .form-control,
  .auth-card .input-group > .form-select {
    border-radius: 10px; /* keep unified rounded corners even in groups */
  }
  .auth-card .form-control:focus,
  .auth-card .form-select:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: none;
  }
  .auth-card .form-text,
  .auth-card .text-muted { color: var(--text-light) !important; }
  .auth-card .form-check-input {
    border: 1px solid var(--border-color);
    box-shadow: none;
  }
  .auth-card .form-check-input:focus { box-shadow: none; }
  .auth-card .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
  }
  .auth-card .btn.btn-primary { height: 48px; }
  .auth-card .btn.rounded-pill { border-radius: 999px; }
  /* Tabs look */
  .auth-card [role="tablist"] { background: #f3f4f6; }
  .auth-card [role="tablist"] .btn.active { background: var(--primary-color); color: #fff; }
  .auth-card [role="tablist"] .btn { color: var(--primary-color); }
  /* Business options */
  .auth-card .business-options .option {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    width: 100%;
    padding: .875rem 1.25rem;
    border: 1.5px solid var(--primary-color);
    border-radius: 999px;
    background: rgba(15, 61, 86, 0.06);
    color: var(--primary-color);
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
  }
  .auth-card .business-options .option .icon { line-height: 0; }
  .auth-card .business-options .option .icon i { font-size: 1.05rem; }
  .auth-card .business-options .option:hover { background: rgba(15, 61, 86, 0.12); transform: translateY(-1px); }
  .auth-card .business-options .option:active { transform: translateY(0); }
  .auth-card .business-options .option:focus-visible { outline: 2px dashed var(--primary-color); outline-offset: 2px; }
</style>

<script>
  // Simple tabs
  (function(){
    const tabs = Array.from(document.querySelectorAll('[role="tablist"] .btn'));
    tabs.forEach(btn => btn && btn.addEventListener('click', () => {
      tabs.forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      const target = document.querySelector(btn.dataset.target);
      document.querySelectorAll('.tab-panel').forEach(p=>{ p.hidden = true; p.classList.remove('show'); });
      target.hidden = false; requestAnimationFrame(()=> target.classList.add('show'));
    }));
  })();

  // Enable Continue when mobile looks valid (>=10 digits)
  (function(){
    const input = document.getElementById('mobileNumber');
    const btn = document.getElementById('continueBtn');
    const onlyDigits = v => (v||'').replace(/\D/g,'');
    function sync(){
      const digits = onlyDigits(input.value);
      input.value = digits; // keep numeric
      btn.disabled = digits.length < 10;
    }
    ['input','keyup','change'].forEach(e=>input.addEventListener(e, sync));
    sync();
  })();

  // Enable Register when fields are valid (name, email, mobile >= 10 digits, terms)
  (function(){
    const nameEl = document.getElementById('regName');
    const emailEl = document.getElementById('regEmail');
    const mobileEl = document.getElementById('regMobile');
    const termsEl = document.getElementById('regTerms');
    const btn = document.getElementById('registerBtn');
    if(!btn) return;
    const emailOk = v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v||'');
    const onlyDigits = v => (v||'').replace(/\D/g,'');
    function sync(){
      if(mobileEl) mobileEl.value = onlyDigits(mobileEl.value);
      const ok = (nameEl.value.trim().length > 0)
        && emailOk(emailEl.value)
        && (onlyDigits(mobileEl.value).length >= 10)
        && termsEl.checked;
      btn.disabled = !ok;
    }
    [nameEl,emailEl,mobileEl,termsEl].forEach(el=>{
      if(el) ['input','change','keyup'].forEach(e=>el.addEventListener(e, sync));
    });
    sync();
  })();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
