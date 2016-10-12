import axios from 'axios';
import * as React from "react";
import TaskPanel from "./TaskPanel";
import * as API from './api/API';
import AuthTokenKeeper from './AuthTokenKeeper';
import {IEdge, IUser, Tag, ITag, User} from "./models/models";
import PrefetchedDataKeeper from "./PrefechedDataKeeper";

interface Props {
}
interface State {
    status?: "logged_out" | "login_pending" | "logged_in" | "error" | "ready";
    fbid?: string;
    userID?: number;
    fbToken?: string;
    authToken?: string;
}

export class Root extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    componentDidMount() {
        this.checkForLogin();
    }

    componentDidUpdate() {
        if (!this.state.status) {
            this.checkForLogin();
            return;
        }

        if (this.state.status === 'login_pending') {
            let params: Map<any, any> = new Map();
            params.set('fbid', this.state.fbid);
            params.set('fbToken', this.state.fbToken);
            API.getGeneric('login', params).then(
                (authResponse) => {
                    // Token we set here will be used by all API calls from this
                    // point onwards.
                    AuthTokenKeeper.setAuthToken(authResponse.authToken);
                    this.setState({
                        status: "logged_in",
                        authToken: authResponse.authToken,
                        userID: authResponse.userID
                    });
                    console.log("state is now ", this.state);
                }, (error) => {
                    console.error("auth failed: ", error);
                    this.setState({status: 'error'});
                });
        }
        if (this.state.status === 'logged_in') {
            let userParams = new Map(),
                tagParams = new Map();
            userParams.set('fields', 'id,name');
            tagParams.set('fields', 'id,caption');
            axios.all([
                API.getGeneric('users', userParams),
                API.getGeneric('tags', tagParams)
            ]).then((response: any) => {
                PrefetchedDataKeeper.keepAllUsers(response[0].nodes.map((user: IUser) => new User(user)));
                PrefetchedDataKeeper.keepAllTags(response[1].nodes.map((tag: ITag) => new Tag(tag)));
                this.setState({status: 'ready'});
            });
        }
    }

    checkForLogin() {
        FB.getLoginStatus((responseObject: Object) => this.statusChangeCallBack(responseObject));
    }

    componentWillReceiveProps(nextProps: Props) {
        this.setState({});
    }

    statusChangeCallBack(res: Object) {
        let response: any = res;
        if (response.status === 'connected') {
            this.setState({
                status: "login_pending",
                fbid: response.authResponse.userID,
                fbToken: response.authResponse.accessToken
            });
        } else {
            this.setState({
                status: "logged_out",
            });
        }
    }

    facebookLogin() {
        FB.login((responseObject) => this.statusChangeCallBack(responseObject));
    }

    render() {
        if (!this.state.status) {
            return (
                <div>Loading</div>
            );
        }
        if (this.state.status === 'logged_out') {
            return (
                <div>
                    <button className="btn btn-primary" onClick={() => this.facebookLogin()}>
                        Login with Facebook
                    </button>
                </div>
            );
        }
        if (this.state.status === 'login_pending') {
            return <div>Signing in...</div>;
        }
        if (this.state.status === 'error') {
            return <div>Error occurred!</div>
        }
        if (this.state.status === 'logged_in') {
            // Now show loading indicator until we have fetched some data like all users and tags.
            return <div>Preparing the tool</div>;
        }
        let userID: number = this.state.userID as number;
        return (
            <div className="root">
                <div className="top-bar">Taskify</div>
                <div className="content">
                    <div className="side-bar"></div>
                    <div className="main-content">
                        <TaskPanel userID={userID}/>
                    </div>
                </div>
            </div>
        );
    }
}
