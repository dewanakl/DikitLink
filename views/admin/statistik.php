<?php parents('layout/home', ['title' => 'Statistik']) ?>

<?php section('home') ?>

<div class="card-body rounded-3 p-2 shadow-sm mb-3" style="background-color: var(--bs-gray-200)">
    <p class="fw-semibold m-1"><i class="fa-solid fa-square-poll-vertical mx-2"></i>Statistik penggunaan</p>
</div>

<canvas style="height:inherit; width:inherit;" id="myChart"></canvas>

<div class="row mb-4">
    <div class="col-md-9">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Hint</th>
                        <th scope="col">User Agent</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php foreach ($user_agent as $ag) : ?>
                        <tr>
                            <th><?= $ag->hint ?></th>
                            <td><?= e($ag->user_agent) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-3 ms-auto">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">Hint</th>
                        <th scope="col">IP Address</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php foreach ($ip_address as $ip) : ?>
                        <tr>
                            <th><?= $ip->hint ?></th>
                            <td><?= e($ip->ip_address) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script defer>
    <?= 'const DATA = ' . json_encode($last_month) . ';' ?>

    let labels = [];
    let values = [];
    let colors = [
        'rgba(75, 192, 192, 0.3)',
        'rgba(54, 162, 235, 0.3)',
        'rgba(153, 102, 255, 0.3)',
        'rgba(255, 206, 86, 0.3)',
        'rgba(255, 159, 64, 0.3)',
        'rgba(255, 99, 132, 0.3)'
    ];
    let border = [
        'rgba(75, 192, 192, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)'
    ];

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    DATA.forEach((key) => {
        labels.push(monthNames[(new Date(key.tgl + '-01').getMonth())] + ' ' + (new Date(key.tgl + '-01').getFullYear()));
        values.push(key.hint);
    });

    // sorting warna
    let valueLen = values.length;
    let valueFloor = valueLen != 1 ? Math.min(...values) : 0;
    let valueRange = Math.max(...values) - valueFloor;
    let maxColorIdx = colors.length - 1;
    let fillColor = [];
    let borderColor = [];
    for (let i = 0; i < valueLen; i++) {
        let normalizedValue = (values[i] - valueFloor) / valueRange;
        let colorIdx = Math.floor(normalizedValue * maxColorIdx);
        colorIdx = Number.isNaN(colorIdx) ? 5 : colorIdx;
        fillColor.push(colors[colorIdx]);
        borderColor.push(border[colorIdx]);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: fillColor,
                    borderColor: borderColor,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>

<?php endsection('home') ?>