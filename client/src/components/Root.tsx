import * as React from "react";
export class Root extends React.Component<{}, {}> {
    render() {
        return (
            <div>
                <div className="topBar"></div>
                <div className="sideBar"></div>
                <div>Content!</div>
            </div>
        );
    }
}
