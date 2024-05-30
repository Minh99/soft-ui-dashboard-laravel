<div class="row">
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
    <div class="card-body p-3">
        <div class="row">
        <div class="col-8">
            <div class="numbers">
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Known</p>
            <h5 class="font-weight-bolder mb-0">
                {{ $known_by_user }}
                <span class="text-success text-sm font-weight-bolder">{{ round($known_by_user * 100 / $count_total,2) }}%</span>
            </h5>
            </div>
        </div>
        <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
            <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
    <div class="card-body p-3">
        <div class="row">
        <div class="col-8">
            <div class="numbers">
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Don't know</p>
            <h5 class="font-weight-bolder mb-0">
                {{ $count_total - $known_by_user }}
                <span class="text-success text-sm font-weight-bolder">{{ round(($count_total - $known_by_user) * 100 / $count_total, 2) }}%</span>
            </h5>
            </div>
        </div>
        <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
            <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
    <div class="card">
    <div class="card-body p-3">
        <div class="row">
        <div class="col-8">
            <div class="numbers">
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Total</p>
            <h5 class="font-weight-bolder mb-0">
                {{ $count_total }}
                <span class="text-danger text-sm font-weight-bolder">Vocabularies</span>
            </h5>
            </div>
        </div>
        <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
            <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
<div class="col-xl-3 col-sm-6">
    <div class="card">
    <div class="card-body p-3">
        <div class="row">
        <div class="col-8">
            <div class="numbers">
            <p class="text-sm mb-0 text-capitalize font-weight-bold">Day/Target</p>
            <h5 class="font-weight-bolder mb-0">
                {{  $userDay->day_number }}/6
                <span class="text-success text-sm font-weight-bolder">{{ round($userDay->day_number * 100 / 6, 2) }}%</span>
            </h5>
            </div>
        </div>
        <div class="col-4 text-end">
            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
            <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>
</div>