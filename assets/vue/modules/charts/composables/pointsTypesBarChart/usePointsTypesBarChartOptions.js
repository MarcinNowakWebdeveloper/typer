import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

export function usePointsTypesBarChartOptions(visibleTypes, pointTypes, sortedUsers) {
    const { t } = useI18n()
    const chartOption = computed(() => {
        const visibleSeries = pointTypes.value
            .filter((type) => visibleTypes.value[type.key])
            .map((type) => ({
                id: type.key,
                name: type.label,
                type: 'bar',
                stack: 'total',
                color: type.color,

                emphasis: {
                    focus: 'series',
                },

                data: sortedUsers.value.map((user) => user['types'][type.key]),
            }))

        return {
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow',
                },

                formatter(params) {
                    const sorted = [...params].sort((a, b) => b.value - a.value)

                    let total = 0

                    sorted.forEach((item) => {
                        total += item.value
                    })

                    let html = `
                    <div style="text-align:center;font-weight:bold;margin-bottom:8px;">
                        ${params[0].axisValue}
                    </div>
                `

                    html += sorted
                        .map((item) => {
                            return `
                            <div>
                                ${item.marker}
                                ${item.seriesName}: ${item.value}
                            </div>
                        `
                        })
                        .join('')

                    html += `
                    <hr style="margin:6px 0;">
                    <div>
                        <strong>${t('charts.pointsTypesBar.options.tooltip.sum')}: ${total}</strong>
                    </div>
                `

                    return html
                },
            },

            legend: {
                show: false,
            },

            grid: {
                left: 20,
                right: 20,
                top: 10,
                bottom: 10,
                containLabel: true,
            },

            xAxis: {
                type: 'value',
            },

            yAxis: {
                type: 'category',
                axisLabel: {
                    show: false,
                },
                data: sortedUsers.value.map((user) => user.userName),
            },

            series: visibleSeries,
        }
    })

    return { chartOption }
}
