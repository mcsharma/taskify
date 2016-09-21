import * as React from "react";
import TaskPanel from "./TaskPanel";

export class Root extends React.Component<{}, {}> {
    render() {
        return (
            <div className="root">
                <div className="top-bar">TASKIFY</div>
                <div className="content">
                    <div className="side-bar"></div>
                    <div className="main-content">
                        <TaskPanel userID={'1'}/>
                    </div>
                </div>
            </div>
        );
    }
}
