import { AUTH_LOGIN, AUTH_LOGOUT, AUTH_ERROR, AUTH_CHECK } from 'react-admin';
import decodeJwt from 'jwt-decode';

const loginUri = `${process.env.REACT_APP_API_ENTRYPOINT}/login_check`;

export default (type, params) => {
    switch (type) {
        case AUTH_LOGIN:
            const { username, password } = params;
            const request = new Request(`${loginUri}`, {
                method: 'POST',
                body: JSON.stringify({ username: username, password: password }),
                headers: new Headers({ 'Content-Type': 'application/json' }),
            });

            return fetch(request)
                .then(response => {
                    if (response.status < 200 || response.status >= 300) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .then(({ token }) => {
                    const decodedToken = decodeJwt(token);
                    localStorage.setItem('token', token);
                    localStorage.setItem('roles', decodedToken.roles);
                    window.location.replace('/');
                });

        case AUTH_LOGOUT:
            localStorage.removeItem('token');
            localStorage.removeItem('roles');
            break;

        case AUTH_ERROR:
            if (401 === params.status || 403 === params.status) {
                localStorage.removeItem('roles');
                localStorage.removeItem('token');
                return Promise.reject();
            }
            break;

        case AUTH_CHECK:
            let roles = localStorage.getItem('roles');
            roles = roles.split(',');
            return localStorage.getItem('token') && roles.includes('ROLE_SUPER_ADMIN') ? Promise.resolve() : Promise.reject();

        default:
            return Promise.resolve();
    }
}
