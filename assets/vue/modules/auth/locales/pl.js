export default {
    loginPage: {
        form: {
            email: 'Email',
            password: 'Hasło',
            submit: 'Zaloguj',
        },
        title: 'Logowanie',
    },
    registerPage: {
        form: {
            email: 'Email',
            name: 'Imię i nazwisko',
            password: 'Hasło',
            submit: 'Rejestruj',
        },
        response: {
            success: 'Konto utworzone. Wysłaliśmy ci e-mail abyś potwierdził swój email.',
        },
        title: 'Rejestracja',
    },
    waitingForActivationPage: {
        title: 'Konto oczekuje na aktywację',
        message:
            'Twój adres email został potwierdzony. W celu aktywacji konta skontaktuj się z administratorem aby potwierdził twój dostęp.',
    },
    verifyFailedPage: {
        title: 'Nie udało się potwierdzić adresu email',
        message: 'Link jest nieprawidłowy lub wygasł.',
    },
}
