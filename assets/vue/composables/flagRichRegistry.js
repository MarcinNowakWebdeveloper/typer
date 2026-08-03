const cache = new Map()

let queue = Promise.resolve()

const loadImage = (url) => {
    return new Promise((resolve) => {
        const img = new Image()

        img.onload = () => resolve(img)
        img.onerror = () => resolve(null)

        img.src = url
    })
}

/**
 * Sequential loader (important for Apache limits)
 */
const enqueue = (task) => {
    queue = queue.then(task).catch(() => {})
    return queue
}

export const buildAxisRich = (labels) => {
    const rich = {}

    return enqueue(async () => {
        for (let i = 0; i < labels.length; i++) {
            const label = labels[i]

            if (typeof label === 'string') {
                continue
            }

            rich[`flag_${i}_1`] = await getFlagStyle(label.home_team_logo_url)
            rich[`flag_${i}_2`] = await getFlagStyle(label.away_team_logo_url)
        }

        return rich
    })
}

const getFlagStyle = async (flagUrl) => {
    if (cache.has(flagUrl)) {
        return cache.get(flagUrl)
    }

    const img = await loadImage(flagUrl)

    const style = {
        height: 18,
        width: 24,
        backgroundColor: {
            image: img,
        },
    }

    cache.set(flagUrl, style)

    return style
}
