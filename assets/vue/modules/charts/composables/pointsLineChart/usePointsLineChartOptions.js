import { computed, ref, watch } from 'vue'
import { buildAxisRich } from '@/composables/flagRichRegistry.js'
import { useAuthStore } from '@/stores/auth.js'
import { useI18n } from 'vue-i18n'

export function usePointsLineChartOptions(currentKey, currentChart, visibleUsers) {
    const { t } = useI18n()
    const auth = useAuthStore()

    const axisRich = ref({})

    watch(
        currentChart,
        async (chart) => {
            if (!chart) {
                axisRich.value = {}
                return
            }

            axisRich.value = await buildAxisRich(chart.labels)
        },
        { immediate: true },
    )

    const chartOption = computed(() => {
        if (!currentChart.value) {
            return {}
        }

        const isMaster = currentKey.value === 'master'

        return {
            tooltip: {
                trigger: 'axis',
                formatter(params) {
                    const sorted = [...params].sort((a, b) => b.value - a.value)

                    let html = `<div style="text-align:center">${params[0].axisValue}</div><br/>`

                    html += sorted
                        .map((item) => {
                            const user = currentChart.value.series.find((s) => s.userName === item.seriesName)

                            const color = `rgb(${user.userColor})`

                            return `
            <div>
                <span style="
                    display:inline-block;
                    width:10px;
                    height:10px;
                    border-radius:50%;
                    background:${color};
                    margin-right:5px;
                "></span>

                <strong>${item.seriesName}</strong>:
                ${item.value}
            </div>
        `
                        })
                        .join('')

                    return html
                },
            },

            legend: { show: false },

            dataZoom: isMaster ? [{ type: 'inside' }, { type: 'slider' }] : [],

            xAxis: {
                type: 'category',
                triggerEvent: true,
                data: currentChart.value.labels.map((l) =>
                    typeof l === 'string'
                        ? l
                        : l.home_team_name
                          ? `${l.home_team_name} VS ${l.away_team_name}`
                          : l.special
                            ? `${l.special}`
                            : '',
                ),

                axisLabel: {
                    interval: 0,
                    rotate: isMaster ? 90 : 0,

                    formatter: (value, index) => {
                        function extracted() {
                            if (typeof currentChart.value.labels[index] === 'string') {
                                return value
                            }
                            if (
                                currentChart.value.labels[index].away_team_logo_url &&
                                axisRich.value?.[`flag_${index}_1`]
                            ) {
                                return `{flag_${index}_1|} VS {flag_${index}_2|}`
                            } else if (currentChart.value.labels[index].special) {
                                return currentChart.value.labels[index].special
                            } else {
                                return t('charts.pointsLine.options.startPoint')
                            }
                        }

                        return extracted()
                    },

                    rich: axisRich.value,
                },
            },

            yAxis: { type: 'value', scale: true },

            series: currentChart.value.series.map((s) => ({
                id: `user-${s.userId}`,
                name: s.userName,
                type: 'line',
                smooth: false,
                data: s.data,
                symbolSize: s.userId === auth.user.id ? 12 : 7,

                lineStyle: {
                    width: s.userId === auth.user.id ? 5 : 2,
                    opacity: visibleUsers.value[s.userId] ? 1 : 0,
                },

                itemStyle: {
                    opacity: visibleUsers.value[s.userId] ? 1 : 0,
                    color: `rgb(${s.userColor})`,
                },
            })),

            grid: {
                left: 20,
                right: 20,
                top: 20,
                bottom: 80,
                containLabel: true,
            },
        }
    })

    return {
        chartOption,
    }
}
