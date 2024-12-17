<style>
  #wrapper .card-body {
    height: 100px;
  }

  .card-body h1 {
    text-align: center;
    text-wrap: nowrap;
    font-size: 26px;
  }

  @media (max-width: 1440px) {
    .card-body h1 {
      font-size: 20px;
    }
  }

  @media (max-width: 860px) {
    .card-body h1 {
      font-size: 20px;
    }
  }
</style>
<div id="wrapper">
      <div class="card">
        <div class="container-fluid">
          <div class="main">
            <div class="row mt-4">
              <div class="col-md-2">
                <div class="row align-items-center box shadow">
                  <div class="col-sm card text-body bg-info mb-3 mx-1 align-items-center zoom">
                    <div class="card-body">
                      <h1 class="card-title text-light h1"><i class="fas fa-users"></i> Клиентов <br><span class="badge bg-light"><?php echo count(get_all_from_table('clients')); ?></span></h1>
                    </div>
                  </div>
                  <div class="col-sm card text-body bg-success mb-3 mx-1 align-items-center zoom">
                    <div class="card-body">
                      <h1 class="card-title text-light h1"><i class="fas fa-users"></i> Специалистов <br><span class="badge bg-light"><?php echo count(get_all_specialists()); ?></span></h1>
                    </div>
                  </div>
                  <div class="col-sm card text-body bg-danger mb-3 mx-1 align-items-center zoom">
                    <div class="card-body">
                      <h1 class="card-title text-light h1"><i class="fas fa-clipboard-list"></i> Заказов <br><span class="badge bg-light"><?php echo count(get_all_from_table('orders')); ?></span></h1>
                    </div>
                  </div>
                  <div class="col-sm card text-body bg-warning mb-3 mx-1 align-items-center zoom">
                    <div class="card-body">
                      <h1 class="card-title text-light h1"><i class="fas fa-clock"></i> Часов <br><span class="badge bg-light"><?php echo money(count_hours_by_forms(get_all_from_table('forms'))); ?></span></h1>
                    </div>
                  </div>
                  <div class="col-sm card text-body bg-primary mb-3 mx-1 align-items-center zoom">
                    <div class="card-body">
                      <h1 class="card-title text-light h1"><i class="fa-solid fa-party-horn"></i> На мероприятия <br><span class="badge bg-light"><?php echo money(intval(count_hours_by_forms(get_all_from_table('forms')) * 333.3)); ?></span></h1>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="content-area card">
        <div class="container-fluid">
          <div class="main">
            <div class="row mt-4">
              <div id="bar" class="col-sm"></div>
              <div id="chart" class="col-sm"></div>
              <div id="holiday" class="col-sm"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="/templates/dashboards/dash_admin.js" defer></script>