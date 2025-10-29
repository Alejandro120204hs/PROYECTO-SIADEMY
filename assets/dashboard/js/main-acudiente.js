// Toggle sidebars
document.getElementById('toggleLeft').addEventListener('click', function() {
  const app = document.getElementById('appGrid');
  const sidebar = document.getElementById('leftSidebar');
  
  if (app.classList.contains('hide-both')) {
    app.classList.remove('hide-both');
    app.classList.add('hide-right');
  } else if (app.classList.contains('hide-left')) {
    app.classList.remove('hide-left');
  } else {
    app.classList.add('hide-left');
  }
  
  sidebar.classList.toggle('hidden');
});

document.getElementById('toggleRight').addEventListener('click', function() {
  const app = document.getElementById('appGrid');
  const rightbar = document.getElementById('rightSidebar');
  
  if (app.classList.contains('hide-both')) {
    app.classList.remove('hide-both');
    app.classList.add('hide-left');
  } else if (app.classList.contains('hide-right')) {
    app.classList.remove('hide-right');
  } else {
    app.classList.add('hide-right');
  }
  
  rightbar.classList.toggle('hidden');
});

// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4', 'Semana 5', 'Semana 6', 'Semana 7', 'Semana 8'],
    datasets: [{
      label: 'Promedio General',
      data: [3.8, 3.9, 4.0, 4.1, 4.0, 4.2, 4.3, 4.2],
      borderColor: '#4f46e5',
      backgroundColor: 'rgba(79, 70, 229, 0.1)',
      tension: 0.4,
      fill: true,
      pointRadius: 5,
      pointBackgroundColor: '#4f46e5',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointHoverRadius: 7
    },
    {
      label: 'Matemáticas',
      data: [4.2, 4.3, 4.5, 4.4, 4.3, 4.5, 4.4, 4.3],
      borderColor: '#ff6b6b',
      backgroundColor: 'rgba(255, 107, 107, 0.1)',
      tension: 0.4,
      fill: true,
      pointRadius: 4,
      pointBackgroundColor: '#ff6b6b',
      pointBorderColor: '#fff',
      pointBorderWidth: 2
    },
    {
      label: 'Tecnología',
      data: [3.0, 3.2, 3.1, 3.3, 3.2, 3.4, 3.5, 3.5],
      borderColor: '#ffb020',
      backgroundColor: 'rgba(255, 176, 32, 0.1)',
      tension: 0.4,
      fill: true,
      pointRadius: 4,
      pointBackgroundColor: '#ffb020',
      pointBorderColor: '#fff',
      pointBorderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
        labels: {
          color: '#e6e9f4',
          padding: 15,
          font: {
            size: 12,
            family: 'Poppins'
          },
          usePointStyle: true,
          pointStyle: 'circle'
        }
      },
      tooltip: {
        backgroundColor: 'rgba(17, 25, 58, 0.95)',
        titleColor: '#fff',
        bodyColor: '#e6e9f4',
        borderColor: 'rgba(255, 255, 255, 0.1)',
        borderWidth: 1,
        padding: 12,
        displayColors: true,
        callbacks: {
          label: function(context) {
            return context.dataset.label + ': ' + context.parsed.y.toFixed(1);
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: false,
        min: 2.5,
        max: 5.0,
        ticks: {
          color: '#a4b1ff',
          font: {
            size: 12
          },
          stepSize: 0.5
        },
        grid: {
          color: 'rgba(255, 255, 255, 0.05)',
          drawBorder: false
        }
      },
      x: {
        ticks: {
          color: '#a4b1ff',
          font: {
            size: 11
          }
        },
        grid: {
          color: 'rgba(255, 255, 255, 0.03)',
          drawBorder: false
        }
      }
    },
    interaction: {
      intersect: false,
      mode: 'index'
    }
  }
});