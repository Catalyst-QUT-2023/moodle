// this script should eventually generate loading animations on page while the url preview is being fetched

import {getUrl} from './repository';

export const getPreviewTemplate = async() => {
    // insert logic to retrieve url from html page?
    const response = await getUrl(url);
    window.console.log(response);
}