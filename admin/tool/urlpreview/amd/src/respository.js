//this script fetches the data required from the external service

import {call as fetchMany} from 'core/ajax';

export const getPreview = (
    url
) => fetchMany([{
    methodname: 'get_url_data',
    args: {
        url
    },
}])[0];