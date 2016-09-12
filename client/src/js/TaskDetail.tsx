import * as React from "react";
import {Task} from './models/models';

interface Props {
    task: Task;
}

interface State {}

export default class TaskDetail extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    public render() {
        let task = this.props.task;
        return (
            <div className="task-detail">
                <div className="task-title">{task.getTitle()}</div>
                    <div>Owner: {task.getOwner()!.getName()}</div>
                    <div>Priority: {task.getPriority()}</div>
            </div>
        );
    }
}


