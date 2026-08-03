export function usePointsLineChartHighlight(chartRef) {
    const highlightUser = (user) => {
        chartRef.value?.chart?.dispatchAction({
            type: 'highlight',
            seriesName: user.userName,
        })
    }

    const downplayUser = (user) => {
        chartRef.value?.chart?.dispatchAction({
            type: 'downplay',
            seriesName: user.userName,
        })
    }

    return {
        highlightUser,
        downplayUser,
    }
}
