import * as React from "react";
import * as ReactDOM from "react-dom";
import {Root} from "./Root";
import "../css/app.less";
import 'react-select/dist/react-select.css';

window.fbAsyncInit = function () {
    FB.init({
        appId: '170551256721403',
        xfbml: true,
        version: 'v2.7'
    });
    ReactDOM.render(
        <Root />,
        document.getElementById("root-container")
    );
};

(function (d, s, id) {
    var js: any, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
