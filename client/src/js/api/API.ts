import axios from 'axios';

export function get<T>(nodeID: number, fields: string) {
    let url = 'http://localhost/api/' + nodeID;
    if (fields) {
        url += '?fields=' + fields;
    }
    return axios.get(url).then((response): T => {
        return response.data as T;
    });
}