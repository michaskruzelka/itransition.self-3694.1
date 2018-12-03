import React from 'react';
import {
    required,
    Edit,
    SimpleForm,
    TextInput,
    BooleanInput
} from 'react-admin';
import { MyReferenceArrayInput } from './component/MyReferenceArrayInput';

const QuizName = ({ record }) => {
    return <span>Quiz {record ? `"${record.name}"` : ''}</span>;
};

export default props => (
    <Edit title={<QuizName/>} {...props}>
        <SimpleForm>
            <TextInput source="name" validate={required()}/>
            <BooleanInput source="isActive" label="Is Active"/>
            <MyReferenceArrayInput/>
        </SimpleForm>
    </Edit>
);
