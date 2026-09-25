import Chart from 'chart.js/auto';

Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#64748b';

const palette = ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#ef4444', '#84cc16'];

export function createChart(canvas, config) {
    const options = {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 500 },
        plugins: {
            legend: {
                labels: { usePointStyle: true, boxWidth: 8 },
            },
        },
    };

    const chart = new Chart(canvas, {
        ...config,
        options: { ...options, ...(config.options || {}) },
    });

    return chart;
}

export function paletteColor(index) {
    return palette[index % palette.length];
}

export function initCharts() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('chart', (configJson) => {
            let chart = null;

            return {
                chart: null,
                init() {
                    const config = typeof configJson === 'string' ? JSON.parse(configJson) : configJson;
                    const ctx = this.$refs.canvas.getContext('2d');
                    this.chart = createChart(ctx, config);
                },
                refresh(configJson) {
                    const config = typeof configJson === 'string' ? JSON.parse(configJson) : configJson;
                    if (this.chart) {
                        this.chart.destroy();
                    }
                    const ctx = this.$refs.canvas.getContext('2d');
                    this.chart = createChart(ctx, config);
                },
            };
        });
    });
}
