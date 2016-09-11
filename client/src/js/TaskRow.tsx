import * as React from "react";
import {Task} from './models/models';

interface Props {
    task: Task;
}

interface State {}

export default class TaskRow extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    public render() {
        let task = this.props.task;
        return (
            <div>
                <span>{task.getOwner()!.getName()}</span>
                <span>{task.getPriority()}</span>
                <span>{task.getTitle()}</span>
                <span>{task.getUpdatedTime()}</span>
            </div>
        );
    }
}


