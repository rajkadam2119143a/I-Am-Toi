document.addEventListener("DOMContentLoaded", function () {
    if (labels && values) {
        const ctx = document.getElementById("chart").getContext("2d");

        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [{
                    label: "Department-wise Conversions",
                    data: values,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: '#333' }
                    },
                    tooltip: {
                        enabled: false,
                        external: function (context) {
                            let tooltipEl = document.getElementById('chartjs-tooltip');
                            if (!tooltipEl) {
                                tooltipEl = document.createElement('div');
                                tooltipEl.id = 'chartjs-tooltip';
                                tooltipEl.innerHTML = '<div class="tooltip-body"></div>';
                                document.body.appendChild(tooltipEl);

                                tooltipEl.addEventListener("mouseenter", () => {
                                    tooltipEl.setAttribute("data-lock", "true");
                                });
                                tooltipEl.addEventListener("mouseleave", () => {
                                    tooltipEl.removeAttribute("data-lock");
                                    tooltipEl.style.opacity = 0;
                                });
                            }

                            const tooltipModel = context.tooltip;
                            if (tooltipModel.opacity === 0 && !tooltipEl.getAttribute("data-lock")) {
                                tooltipEl.style.opacity = 0;
                                return;
                            }

                            let title = tooltipModel.title[0];
                            let value = tooltipModel.dataPoints[0].raw;
                            let innerHtml = `<div class="tooltip-title">${title} ${value}</div>`;

                            // ✅ Show breakdown if exists
                            if (breakdownData[title]) {
							let filtered = breakdownData[title].filter(item => {
								let parts = item.trim().split(" ");
								let val = parseInt(parts.pop());
								return val > 0;
							});
							if (filtered.length > 0) {
								innerHtml += '<div class="tooltip-list"><ul>';
								filtered.forEach(function (item) {
									innerHtml += `<li>${item}</li>`;
								});
								innerHtml += '</ul></div>';
							}
						}


                            tooltipEl.querySelector('.tooltip-body').innerHTML = innerHtml;

                            const position = context.chart.canvas.getBoundingClientRect();
                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.position = 'absolute';
                            tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                            tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                            tooltipEl.style.pointerEvents = 'auto';
                        }
                    }
                }
            }
        });
    }
});

