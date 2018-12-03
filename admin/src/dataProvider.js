import apiClient from './apiClient';
import { fetchHydra as baseFetchHydra } from '@api-platform/admin';

const fetchHeaders = {'Authorization': `Bearer ${window.localStorage.getItem('token')}`};

const fetchHydra = (url, options = {}) => {
    return baseFetchHydra(url, {
        ...options,
        headers: new Headers(fetchHeaders)});
};

const dataProvider = api => apiClient(api, fetchHydra);

export default dataProvider;
