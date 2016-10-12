import axios from 'axios';
import {Promise} from "axios";
import * as URI from "urijs";
import AuthTokenKeeper from '../AuthTokenKeeper';

export function get<T>(nodeID: string, fields: string): Promise<T>  {
    let uri = URI('/api').segment(nodeID);
    uri.setQuery('authToken', AuthTokenKeeper.getAuthToken());
    if (fields) {
        uri.setQuery('fields', fields);
    }
    return axios.get(uri.toString()).then((response): T => {
        return response.data as T;
    });
}

export function getGeneric(path: string, params: Map<string, string>) {
    let uri = URI('/api').segment(path);
    params.set('authToken', AuthTokenKeeper.getAuthToken());
    params.forEach((value, key) => {
        uri.setQuery(key, value);
    });
    return axios.get(uri.toString()).then((response) => {
        return response.data;
    });
}

export function post(path: string, params: Map<string, string>) {
    let uri = URI('/api').segment(path);
    uri.setQuery("method", "post");
    params.set('authToken', AuthTokenKeeper.getAuthToken());
    params.forEach((value, key) => {
        uri.setQuery(key, value);
    });
    return axios.get(uri.toString()).then((response) => {
       return response.data;
    });
}