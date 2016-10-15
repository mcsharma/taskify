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
        let line: any | null = null;
        switch (activity.getChanged()) {
            // TODO add TaskField enum in JS code that matches exactly with server enum
            case 'title':
                line = <span>changed the title from {activity.getOldTitle()} to {activity.getNewTitle()}</span>;
                break;
            case 'description':
                line = <span>changed the description from {activity.getOldDescription()} to {activity.getNewDescription()}</span>;
                break;
            case 'status':
                if (activity.getNewStatus() == 'open') {
                    line = <span>reopened the task</span>;
                } else {
                    line = <span>closed the task</span>
                }
                break;
            case 'priority':
                line = <span>changed the priority to {activity.getNewPriority()}</span>;
                break;
            case 'owner':
                if (!!activity.getNewOwner()) {
                    if (activity.getActor().getID() === activity.getNewOwner()!.getID()) {
                        line = <span>claimed the task</span>;
                    } else {
                        line = <span>assigned the task to {activity.getNewOwner()!.getName()}</span>;
                    }
                } else {
                    line = <span>placed the task up for grabs</span>;
                }
                break;
            case 'tags':
                let addedList: string = '', removedList: string = '';
                if (!!activity.getAddedTags()) {
                    addedList = activity.getAddedTags().map((tag) => tag.getCaption()).join(', ');
                }
                if (!!activity.getRemovedTags()) {
                    removedList = activity.getRemovedTags().map(tag => tag.getCaption()).join(', ');
                }
                if (addedList && removedList) {
                    line = <span>changed the tags: added {addedList}, removed {removedList}</span>;
                } else if (addedList) {
                    line = <span>added tags {addedList}</span>;
                } else if (removedList) {
                    line = <span>removed tags {removedList}</span>;
                }
                break;
            case 'subscribers':
                let addedSubscribers: string = '', removedSubscribers: string = '';
                if (!!activity.getAddedSubscribers()) {
                    addedSubscribers = activity.getAddedSubscribers().map(user => user.getName()).join(', ');
                }
                if (!!activity.getRemovedSubscribers()) {
                    removedSubscribers = activity.getRemovedSubscribers().map(user => user.getName()).join(', ');
                }
                if (addedSubscribers && removedSubscribers) {
                    line = <span>changed the subscribers: added {addedSubscribers}, removed {removedSubscribers}</span>;
                } else if (addedSubscribers) {
                    line = <span>added subscribers {addedSubscribers}</span>;
                } else if (removedSubscribers) {
                    line = <span>removed subscribers {removedSubscribers}</span>;
                }
                break;
            default:
                console.error("unhandled activity type ", activity.getChanged());
        }
        return (
            <div className="tk-activity">
                <strong>{activity.getActor().getName()}</strong>{' '}
                {line}
            </div>
        );
    }
}
