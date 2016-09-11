import * as React from "react";
import TaskPanel from "./TaskPanel";

export class Root extends React.Component<{}, {}> {
    render() {
        return (
            <div className="root">
                <div className="topBar"></div>
                <div className="sideBar"></div>
                <TaskPanel userID={1} />
            </div>
        );
    }
}
