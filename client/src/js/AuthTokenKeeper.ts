
export default class AuthTokenKeeper {

    private static authToken: string;

    public static setAuthToken(authToken: string): void {
        AuthTokenKeeper.authToken = authToken;
    }

    public static getAuthToken(): string {
        return AuthTokenKeeper.authToken;
    }
}