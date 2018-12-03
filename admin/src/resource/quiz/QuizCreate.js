import React from 'react';
import {
    required,
    Create,
    SimpleForm,
    TextInput,
    BooleanInput
} from 'react-admin';
import { MyReferenceArrayInput } from './component/MyReferenceArrayInput';

export default props => (
    <Create {...props}>
        <SimpleForm redirect="list">
            <TextInput source="name" validate={required()}/>
            <BooleanInput source="isActive" label="Is Active"/>
            <MyReferenceArrayInput/>
        </SimpleForm>
    </Create>
);
