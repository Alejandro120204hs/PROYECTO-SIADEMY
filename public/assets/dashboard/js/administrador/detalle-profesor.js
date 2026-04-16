document.getElementById('toggleLeft').addEventListener('click', function () {
  document.getElementById('leftSidebar').classList.toggle('hidden');
  document.getElementById('appGrid').classList.toggle('hide-left');
});

const tabButtons = document.querySelectorAll('.tab-btn');
const tabPanes = document.querySelectorAll('.tab-pane');

tabButtons.forEach(function (button) {
  button.addEventListener('click', function () {
    const tabId = button.getAttribute('data-tab');

    tabButtons.forEach(function (btn) { btn.classList.remove('active'); });
    tabPanes.forEach(function (pane) { pane.classList.remove('active'); });

    button.classList.add('active');
    document.getElementById(tabId).classList.add('active');
  });
});

const ctx = document.getElementById('performanceChart');
if (ctx) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
      datasets: [{
        label: 'Promedio Estudiantes',
        data: [4.1, 4.2, 4.3, 4.2, 4.4, 4.3],
        borderColor: '#667eea',
        backgroundColor: 'rgba(102, 126, 234, 0.1)',
        tension: 0.4,
        fill: true
      }, {
        label: 'Evaluacion Docente',
        data: [4.6, 4.7, 4.6, 4.8, 4.7, 4.8],
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: {
            color: '#e6e9f4',
            font: {
              family: 'Poppins',
              size: 12
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: false,
          min: 3.5,
          max: 5.0,
          ticks: {
            color: '#97a1b6',
            font: {
              family: 'Poppins'
            }
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.08)'
          }
        },
        x: {
          ticks: {
            color: '#97a1b6',
            font: {
              family: 'Poppins'
            }
          },
          grid: {
            color: 'rgba(255, 255, 255, 0.08)'
          }
        }
      }
    }
  });
}
