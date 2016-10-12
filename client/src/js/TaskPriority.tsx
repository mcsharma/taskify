import * as React from "react";
import '../css/TaskPriority.less';
import {Priority} from "./metadata/Priority";

interface Props {
    priority: Priority;
}

interface State {}
export default class TaskPriority extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    componentWillReceiveProps(newProps: Props) {
        this.state = {};
    }

    render() {
        return (
            <div className={"tk-priority "+"tk-priority-"+this.props.priority}>
                {this.props.priority}
            </div>
        );
    }
}
