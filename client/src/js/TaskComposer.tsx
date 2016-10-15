import * as React from "react";
import '../css/TaskPriority.less';
import {Priority, PriorityEnum} from "./metadata/Priority";
import {Table, Modal, Button, Input} from "react-bootstrap";
import ReactSelectClass = require("react-select");
import Option = ReactSelectClass.Option;
import PrefetchedDataKeeper from "./PrefechedDataKeeper";
import {Task, User} from "./models/models";
import '../css/TaskComposer.less';
import * as API from './api/API';
import AuthTokenKeeper from "./AuthTokenKeeper";

interface Props {
    viewer: number;
    show: boolean;
    onCancel: () => void;
    onPublish: (taskID: number) => void;
}

interface State {
    title?: string;
    description?: string;
    priority?: Option;
    owner?: Option;
    tags?: Option[];
    subscribers?: Option[];
}

export default class TaskComposer extends React.Component<Props, State> {

    constructor(props: Props) {
        super(props);
        this.state = {
            priority: {
                label: PriorityEnum.UNSPECIFIED,
                value: PriorityEnum.UNSPECIFIED
            }
        };
    }

    render() {
        return (
            <Modal dialogClassName="task-composer" show={this.props.show} onHide={() => this.props.onCancel()}>
                <Modal.Header closeButton>
                    <Modal.Title>Create New Task</Modal.Title>
                </Modal.Header>
                <Modal.Body>
                    <div className="composer-field title">
                        <div className="name">Title:</div>
                        <input value={this.state.title}
                               onChange={(event: any) => this.setState({title: event.currentTarget.value})}
                        />
                    </div>
                    <div className="composer-field owner">
                        <div className="name">Owner:</div>
                        <ReactSelectClass options={PrefetchedDataKeeper.getUserTypeaheadOptions()}
                                          value={this.state.owner}
                                          placeholder="owner"
                                          autosize={true}
                                          onChange={(option: Option) => this.setState({owner: option})}
                        />
                    </div>
                    <div className="composer-field priority">
                        <div className="name">Priority:</div>
                        <ReactSelectClass
                            searchable={false}
                            clearable={false}
                            value={this.state.priority || PriorityEnum.UNSPECIFIED}
                            options={PriorityEnum.getAll<string>().map(p => {return {value: p, label: p};})}
                            onChange={(option: Option) => option ? this.setState({priority: option}) : null}
                        />
                    </div>
                    <div className="composer-field description">
                        <div className="name">Description:</div>
                        <textarea className="content" value={this.state.description}
                                  onChange={(event) => this.setState({description: event.currentTarget.value})}
                        />
                    </div>
                    <div className="composer-field tags">
                        <div className="name">Tags:</div>
                        <ReactSelectClass multi={true}
                                          clearable={false}
                                          options={PrefetchedDataKeeper.getTagTypeaheadOptions()}
                                          value={this.state.tags}
                                          placeholder="Add some tags.."
                                          onChange={(options: Option[]) => options ? this.setState({tags: options}) : null}
                        />
                    </div>
                    <div className="composer-field subscribers">
                        <div className="name">Subscribers</div>
                        <ReactSelectClass multi={true}
                                          clearable={false}
                                          options={PrefetchedDataKeeper.getUserTypeaheadOptions()}
                                          value={this.state.subscribers}
                                          placeholder="Add subscribers.."
                                          onChange={(options: Option[]) => options ? this.setState({subscribers: options}) : null}
                        />
                    </div>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={() => this.props.onCancel()}>Close</Button>
                    <Button bsStyle="primary" onClick={() => this.onPublishClick()}>Publish</Button>
                </Modal.Footer>
            </Modal>
        );
    }

    private onPublishClick() {
        let params = new Map();
        params.set('title', this.state.title);
        params.set('description', this.state.description);
        params.set('priority', this.state.priority!.value);
        params.set('owner_id', this.state.owner && this.state.owner.value);
        if (!!this.state.tags) {
            params.set('tags', JSON.stringify(this.state.tags.map(option => option.value)));
        }
        if (!!this.state.subscribers) {
            params.set('subscribers', JSON.stringify(this.state.subscribers.map(option => option.value)));
        }
        API.post(''+this.props.viewer+'/created_tasks', params).then((response: any) => {
            this.props.onPublish(response.id);
        });
    }
}
