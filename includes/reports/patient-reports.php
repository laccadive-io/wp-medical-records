<h1>Patients</h1>
<div id="canvas"></div>
<?php
    $args = [
        'post_type' => 'patients'
    ];
    $query = new WP_Query( $args );
?>
<canvas id="myChart" width="1200" height="500"></canvas>
<script>
    var data = <?php echo json_encode($query->posts); ?>;
    
    var chartData = [];

    var months = [
        "Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ];

    const filteredData =  data.filter(currentItem => {
        const date = new Date(currentItem.post_date);
        var timeDiff = Math.abs(new Date() - date);
        var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
        if(diffDays < 365) return true;
    });
    
    filteredData.forEach(item => {
        var date = item.post_date;
        date = new Date(date);
        var matched = chartData.filter(item => item.month === date.getMonth() && item.year === date.getFullYear())
        if(matched.length > 0) {
            var tempObj = {
                ...matched[0],
                patients: matched[0].patients + 1
            }
            chartData = [
                ...chartData.filter(item => item.month !== date.getMonth() && item.year !== date.getFullYear()),
                tempObj
            ]
        } else {
            chartData.push({
                month: date.getMonth(),
                year: date.getFullYear(),
                patients: 1,
                displayMonth: months[date.getMonth()] + ' ' + date.getFullYear()
            })
        }
    })
    
    var todayDate = new Date();
    
    var j = 0;
    for (let i=todayDate.getMonth();;i--) {
        let m = i;
        let y = todayDate.getFullYear();
        if (m < 0) {
            m = 12 + i;
            y = todayDate.getFullYear() - 1;
        }

        const filterData1 = chartData.filter(item => item.month === m && item.year === y);
        if (filterData1.length === 0) {
            chartData.unshift({
            month: m,
            year: y,
            patients: 0,
            displayMonth: months[m] + ' ' + y
            })
        }

        if (j == 11)
            break;

        j++;
    }

    chartData = chartData.sort((a, b) => {
        return new Date(a.year,  a.month, 1) - new Date(b.year, b.month, 1)
        //return a.year > b.year || a.month > b.month ? 1 : a.year < b.year || a.month < b.month ? -1 : 0;
    });

    var labels = [];
    var feedData = [];
    chartData.forEach(item => {
        labels.push(item.displayMonth);
        feedData.push(item.patients);
    })
    
    var ctx = document.getElementById("myChart").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '# of Patients per Month',
                data: feedData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            },
            responsive: false
        }
    });
</script>