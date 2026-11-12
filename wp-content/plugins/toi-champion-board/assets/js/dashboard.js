document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("chart").getContext("2d");
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Editorial", "Marketing", "Sales", "Digital", "Operations"],
            datasets: [{
                label: "Active Employees",
                data: [45, 38, 42, 35, 28],
                backgroundColor: "#d32f2f",
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: "#333",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    callbacks: {
                        // Set the tooltip title to the category name
                        title: function (context) {
                            return context[0].chart.data.labels[context[0].dataIndex];
                        },
                        // Show only the value (or customize if needed)
                        label: function (context) {
                            return `${context.dataset.data[context.dataIndex]} active employees`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 60,
                    grid: { color: "#eee" },
                    ticks: { color: "#666" }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: "#666" }
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
  const navLinks = document.querySelectorAll('.nav a');

  // Restore active from localStorage (optional)
  const saved = localStorage.getItem('tcb_active_nav');
  if (saved) {
    navLinks.forEach(link => link.classList.toggle('active', link.getAttribute('href') === saved));
  }

  navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
      // allow default if you want the hash in URL (we'll still smooth-scroll)
      e.preventDefault();  

      // set active class
      navLinks.forEach(l => l.classList.remove('active'));
      this.classList.add('active');

      // save for persistence across refresh (optional)
      localStorage.setItem('tcb_active_nav', this.getAttribute('href'));

      // Smooth scroll to target id
      const targetId = this.getAttribute('href').replace('#', '');
      const targetEl = document.getElementById(targetId);
      if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // update the address bar without jumping
        history.replaceState(null, '', `#${targetId}`);
      }
    });
  });

  // If page loads with a hash, scroll to it and set active
  if (window.location.hash) {
    const id = window.location.hash;
    const link = document.querySelector(`.nav a[href="${id}"]`);
    if (link) {
      link.classList.add('active');
      const el = document.querySelector(id);
      if (el) el.scrollIntoView();
    }
  }
});


