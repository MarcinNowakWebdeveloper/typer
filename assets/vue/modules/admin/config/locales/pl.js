export default {
    configPage: {
        groups: 'Grupy',
        teams: 'Zespoły',
        title: 'Konfiguracja',
        stages: 'Etapy',
    },
    game: {
        form: {
            title: {
                new: 'Dodaj mecz w grupie {groupName} etapu {stageName}',
                edit: 'Edytuj mecz w grupie {groupName} etapu {stageName}',
            },
            fields: {
                awayGoals: 'Bramki przyjezdnych',
                awayTeam: 'Drużyna przyjezdna',
                date: 'Data',
                homeGoals: 'Bramki gospodarzy',
                homeTeam: 'Drużyna gospodarzy',
                time: 'Godzina',
            },
            placeholder: {
                searchTeam: 'Wyszukaj drużynę',
            },
        },
    },
    groupPage: {
        form: {
            title: {
                new: 'Dodaj grupę',
                edit: 'Edytuj grupę',
            },
            placeholder: {
                name: 'Nazwa grupy',
                teamSearch: 'Wyszukaj drużynę',
            },
        },
    },
    teamPage: {
        form: {
            title: {
                edit: 'Edytuj drużynę',
                new: 'Dodaj drużynę',
            },
            file: 'Wybierz plik',
            placeholder: {
                name: 'Nazwa drużyny',
            },
        },
    },
    stagePage: {
        form: {
            title: {
                edit: 'Edytuj etap',
                new: 'Dodaj etap',
            },
            placeholder: {
                groupSearch: 'Wyszukaj grupę',
                teamSearch: 'Nazwa etapu',
                shortName: 'Skrócona nazwa etapu',
            },
        },
        stageGroup: {
            teams: 'Zespoły',
        },
    },
}
