export const getTextColor = (rgb) => {
    const match = rgb.match(/\d+/g)

    if (!match || match.length < 3) {
        return '#212529'
    }

    const [r, g, b] = match.map(Number)

    const brightness = (r * 299 + g * 587 + b * 114) / 1000

    return brightness > 150 ? '#212529' : '#f8f9fa'
}
