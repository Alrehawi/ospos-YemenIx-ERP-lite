<?= view('partial/header') ?>

<div class="container" >
    <div class="page-header">
        <h1><i class="fa fa-calculator"></i> المحاسبة</h1>
    </div>

   
    <div class="row text-center rtl-row">
        <div class="col-md-3">
            <a href="<?= site_url('accounting/accounts') ?>" class="btn btn-primary btn-lg btn-block">
                <i class="fa fa-list"></i><br><br>
                دليل الحسابات
            </a>
        </div>

        <div class="col-md-3">
            <a href="<?= site_url('accounting/journal') ?>" class="btn btn-success btn-lg btn-block">
                <i class="fa fa-book"></i><br><br>
                القيود اليومية
            </a>
        </div>

        <div class="col-md-3">
            <a href="<?= site_url('accounting/ledger') ?>" class="btn btn-info btn-lg btn-block">
                <i class="fa fa-table"></i><br><br>
                دفتر الأستاذ
            </a>
        </div>

        <div class="col-md-3">
            <a href="<?= site_url('accounting/reports') ?>" class="btn btn-warning btn-lg btn-block">
                <i class="fa fa-bar-chart"></i><br><br>
                التقارير
            </a>
        </div>

    </div>
</div>

<?= view('partial/footer') ?>