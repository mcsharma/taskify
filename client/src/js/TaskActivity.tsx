import * as React from "react";
import {Activity} from "./models/models";

interface State {}

interface Props {
    activity: Activity;
}

export default class TaskActivity extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    public render() {
        let activity = this.props.activity;
        let from: any = null, to: any = null;
        switch (activity.getChanged()) {
            // TODO add TaskField enum in JS code that matches exactly with server enum
            case 'title':
                from = activity.getOldTitle();
                to = activity.getNewTitle();
                break;
            case 'description':
                from = activity.getOldDescription();
                to = activity.getNewDescription();
                break;
            case 'status':
                to = activity.getNewStatus();
                break;
            case 'priority':
                from = activity.getOldPriority();
                to = activity.getNewPriority();
                break;
            default:
                console.error("unhandled activity type ", activity.getChanged());
        }
        return (
            <div className="tk-activity">
                <strong>{activity.getActor().getName()}</strong>
                &nbsp;changed the {activity.getChanged()}
                {from ? <span>&nbsp;from <strong>{from}</strong></span> : null}
                &nbsp;to <strong>{to}</strong>
                .
            </div>
        );
    }
}
