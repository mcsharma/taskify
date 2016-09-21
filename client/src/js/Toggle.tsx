import * as React from "react";
import '../css/Toggle.less';

interface Props {
    active: boolean;
    onChange(active: boolean): void;
}

interface State {
    active?: boolean;
}

export default class Toggle extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {};
    }

    componentWillReceiveProps(newProps: Props) {
        this.state = {};
    }

    render() {
        var isActive = this.isActive();
        return (
            <div className={"toggle-button "+ (isActive ? 'active' : 'passive')}
                onClick={(event) => this.onClick(event)}>
                <div className="left">{isActive ? 'open' : null}</div>
                <div className="right">{!isActive ? 'closed' : null}</div>
            </div>
        );
    }

    onClick(event: React.MouseEvent<HTMLDivElement>) {
        this.setState({active: !this.isActive()});
        this.props.onChange(!this.isActive());
    }

    isActive() {
        if (this.state.active !== void 0) {
            return this.state.active;
        }
        return this.props.active;
    }
}
