/**
 * Program Criteria Builder
 * Handles dynamic form building for program eligibility criteria
 */

// Available fields for criteria
const AVAILABLE_FIELDS = {
    // Demographics
    'age': { label: 'Age', type: 'number' },
    'gender': { label: 'Gender', type: 'select', options: ['Male', 'Female'] },
    'marital_status': { label: 'Marital Status', type: 'select', options: ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'] },
    'employment_status': { label: 'Employment Status', type: 'select', options: ['Unemployed', 'Part-time', 'Self-employed', 'Full-time'] },
    'income_level': { label: 'Income Level', type: 'select', options: ['Low', 'Lower Middle', 'Middle', 'Upper Middle', 'High'] },
    'education_level': { label: 'Education Level', type: 'select', options: ['No Education', 'Elementary', 'High School', 'Vocational', 'College', 'Post Graduate'] },
    'family_size': { label: 'Family Size', type: 'number' },
    'is_pwd': { label: 'Is PWD', type: 'boolean' },
    'occupation': { label: 'Occupation', type: 'text' },
    'purok': { label: 'Purok', type: 'text' },
    
    // Blotter
    'blotter.total_count': { label: 'Blotter Total Count', type: 'number' },
    'blotter.recent_count': { label: 'Blotter Recent Count', type: 'number' },
    'blotter.has_recent_incidents': { label: 'Has Recent Incidents', type: 'boolean' },
    
    // Medical
    'medical.has_chronic_conditions': { label: 'Has Chronic Conditions', type: 'boolean' },
    'medical.has_recent_visits': { label: 'Has Recent Visits', type: 'boolean' },
    'medical.total_visits': { label: 'Medical Total Visits', type: 'number' },
    'medical.recent_visits': { label: 'Medical Recent Visits', type: 'number' },
};

// Available operators
const AVAILABLE_OPERATORS = {
    'equals': 'Equals',
    'not_equals': 'Not Equals',
    'in': 'In (one of)',
    'not_in': 'Not In',
    'greater_than': 'Greater Than',
    'less_than': 'Less Than',
    'greater_than_or_equal': 'Greater Than or Equal',
    'less_than_or_equal': 'Less Than or Equal',
};

let conditionCounter = 0;

/**
 * Initialize the criteria builder
 */
function initializeCriteriaBuilder(existingCriteria = null) {
    const container = document.getElementById('conditions-container');
    if (!container) return;
    
    container.innerHTML = '';
    conditionCounter = 0;
    
    if (existingCriteria && existingCriteria.conditions && existingCriteria.conditions.length > 0) {
        // Load existing criteria
        existingCriteria.conditions.forEach(condition => {
            if (condition.field) {
                addConditionElement(condition);
            } else if (condition.operator) {
                addGroupElement(condition);
            }
        });
        
        // Set root operator
        const rootOperator = document.getElementById('root-operator');
        if (rootOperator && existingCriteria.operator) {
            rootOperator.value = existingCriteria.operator;
        }
    } else {
        // Start with one empty condition
        addCondition();
    }
    
    updateCriteriaJSON();
}

/**
 * Add a new condition
 */
function addCondition(existingCondition = null) {
    const container = document.getElementById('conditions-container');
    if (!container) return;
    
    const conditionId = 'condition-' + (++conditionCounter);
    const condition = existingCondition || {};
    
    const conditionHtml = `
        <div class="condition-item border border-gray-300 rounded-lg p-4 bg-gray-50" data-id="${conditionId}">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Field</label>
                        <select class="condition-field w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionField('${conditionId}', this.value)">
                            <option value="">Select Field</option>
                            ${Object.keys(AVAILABLE_FIELDS).map(field => 
                                `<option value="${field}" ${condition.field === field ? 'selected' : ''}>${AVAILABLE_FIELDS[field].label}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operator</label>
                        <select class="condition-operator w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionOperator('${conditionId}', this.value)">
                            <option value="">Select Operator</option>
                            ${Object.keys(AVAILABLE_OPERATORS).map(op => 
                                `<option value="${op}" ${condition.operator === op ? 'selected' : ''}>${AVAILABLE_OPERATORS[op]}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Value</label>
                        <div class="condition-value-container">
                            ${getValueInput(condition.field || '', condition.operator || '', condition.value || '')}
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeCondition('${conditionId}')" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', conditionHtml);
    
    // If field and operator are set, update the value input
    if (condition.field && condition.operator) {
        updateConditionField(conditionId, condition.field);
        updateConditionOperator(conditionId, condition.operator);
    }
    
    updateCriteriaJSON();
}

/**
 * Add a new group (nested conditions)
 */
function addGroup(existingGroup = null) {
    const container = document.getElementById('conditions-container');
    if (!container) return;
    
    const groupId = 'group-' + (++conditionCounter);
    const group = existingGroup || { operator: 'AND', conditions: [] };
    
    const groupHtml = `
        <div class="group-item border-2 border-green-300 rounded-lg p-4 bg-green-50" data-id="${groupId}">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <label class="block text-sm font-medium text-gray-700">Group Operator:</label>
                    <select class="group-operator border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateCriteriaJSON()">
                        <option value="AND" ${group.operator === 'AND' ? 'selected' : ''}>AND</option>
                        <option value="OR" ${group.operator === 'OR' ? 'selected' : ''}>OR</option>
                    </select>
                </div>
                <button type="button" onclick="removeGroup('${groupId}')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i> Remove Group
                </button>
            </div>
            <div class="group-conditions space-y-3" data-group-id="${groupId}">
                ${group.conditions && group.conditions.length > 0 
                    ? group.conditions.map(c => c.field ? addConditionElement(c, groupId) : addGroupElement(c, groupId)).join('')
                    : '<p class="text-sm text-gray-500">No conditions in this group. Add conditions below.</p>'
                }
            </div>
            <div class="mt-3 flex gap-2">
                <button type="button" onclick="addConditionToGroup('${groupId}')" 
                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i>Add Condition
                </button>
                <button type="button" onclick="addGroupToGroup('${groupId}')" 
                        class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition-colors">
                    <i class="fas fa-layer-group mr-1"></i>Add Nested Group
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', groupHtml);
    
    // Recursively add conditions to the group
    if (group.conditions && group.conditions.length > 0) {
        const groupContainer = container.querySelector(`[data-id="${groupId}"] .group-conditions`);
        groupContainer.innerHTML = '';
        group.conditions.forEach(condition => {
            if (condition.field) {
                addConditionToGroup(groupId, condition);
            } else if (condition.operator) {
                addGroupToGroup(groupId, condition);
            }
        });
    }
    
    updateCriteriaJSON();
}

/**
 * Add condition element (helper for loading existing data)
 */
function addConditionElement(condition, parentGroupId = null) {
    const conditionId = 'condition-' + (++conditionCounter);
    const container = parentGroupId 
        ? document.querySelector(`[data-group-id="${parentGroupId}"]`)
        : document.getElementById('conditions-container');
    
    if (!container) return '';
    
    const conditionHtml = `
        <div class="condition-item border border-gray-300 rounded-lg p-3 bg-white" data-id="${conditionId}" data-parent-group="${parentGroupId || ''}">
            <div class="flex items-start justify-between">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Field</label>
                        <select class="condition-field w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionField('${conditionId}', this.value)">
                            <option value="">Select Field</option>
                            ${Object.keys(AVAILABLE_FIELDS).map(field => 
                                `<option value="${field}" ${condition.field === field ? 'selected' : ''}>${AVAILABLE_FIELDS[field].label}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Operator</label>
                        <select class="condition-operator w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionOperator('${conditionId}', this.value)">
                            <option value="">Select Operator</option>
                            ${Object.keys(AVAILABLE_OPERATORS).map(op => 
                                `<option value="${op}" ${condition.operator === op ? 'selected' : ''}>${AVAILABLE_OPERATORS[op]}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Value</label>
                        <div class="condition-value-container">
                            ${getValueInput(condition.field || '', condition.operator || '', condition.value || '')}
                        </div>
                    </div>
                </div>
                <button type="button" onclick="removeCondition('${conditionId}')" 
                        class="ml-2 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', conditionHtml);
    
    if (condition.field && condition.operator) {
        updateConditionField(conditionId, condition.field);
        updateConditionOperator(conditionId, condition.operator);
    }
    
    return '';
}

/**
 * Add group element (helper for loading existing data)
 */
function addGroupElement(group, parentGroupId = null) {
    // This will be handled by addGroup recursively
    return '';
}

/**
 * Add condition to a specific group
 */
function addConditionToGroup(groupId, existingCondition = null) {
    const groupContainer = document.querySelector(`[data-group-id="${groupId}"]`);
    if (!groupContainer) return;
    
    const conditionId = 'condition-' + (++conditionCounter);
    const condition = existingCondition || {};
    
    const conditionHtml = `
        <div class="condition-item border border-gray-300 rounded-lg p-3 bg-white" data-id="${conditionId}" data-parent-group="${groupId}">
            <div class="flex items-start justify-between">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Field</label>
                        <select class="condition-field w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionField('${conditionId}', this.value)">
                            <option value="">Select Field</option>
                            ${Object.keys(AVAILABLE_FIELDS).map(field => 
                                `<option value="${field}" ${condition.field === field ? 'selected' : ''}>${AVAILABLE_FIELDS[field].label}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Operator</label>
                        <select class="condition-operator w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateConditionOperator('${conditionId}', this.value)">
                            <option value="">Select Operator</option>
                            ${Object.keys(AVAILABLE_OPERATORS).map(op => 
                                `<option value="${op}" ${condition.operator === op ? 'selected' : ''}>${AVAILABLE_OPERATORS[op]}</option>`
                            ).join('')}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Value</label>
                        <div class="condition-value-container">
                            ${getValueInput(condition.field || '', condition.operator || '', condition.value || '')}
                        </div>
                    </div>
                </div>
                <button type="button" onclick="removeCondition('${conditionId}')" 
                        class="ml-2 px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    groupContainer.insertAdjacentHTML('beforeend', conditionHtml);
    
    if (condition.field && condition.operator) {
        updateConditionField(conditionId, condition.field);
        updateConditionOperator(conditionId, condition.operator);
    }
    
    updateCriteriaJSON();
}

/**
 * Add nested group to a group
 */
function addGroupToGroup(parentGroupId, existingGroup = null) {
    const groupContainer = document.querySelector(`[data-group-id="${parentGroupId}"]`);
    if (!groupContainer) return;
    
    const groupId = 'group-' + (++conditionCounter);
    const group = existingGroup || { operator: 'AND', conditions: [] };
    
    const groupHtml = `
        <div class="group-item border-2 border-green-300 rounded-lg p-3 bg-green-50" data-id="${groupId}" data-parent-group="${parentGroupId}">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <label class="block text-xs font-medium text-gray-700">Operator:</label>
                    <select class="group-operator border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateCriteriaJSON()">
                        <option value="AND" ${group.operator === 'AND' ? 'selected' : ''}>AND</option>
                        <option value="OR" ${group.operator === 'OR' ? 'selected' : ''}>OR</option>
                    </select>
                </div>
                <button type="button" onclick="removeGroup('${groupId}')" 
                        class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="group-conditions space-y-2" data-group-id="${groupId}">
                ${group.conditions && group.conditions.length > 0 
                    ? group.conditions.map(c => c.field ? addConditionToGroup(groupId, c) : addGroupToGroup(groupId, c)).join('')
                    : '<p class="text-xs text-gray-500">No conditions</p>'
                }
            </div>
            <div class="mt-2 flex gap-2">
                <button type="button" onclick="addConditionToGroup('${groupId}')" 
                        class="px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i>Condition
                </button>
                <button type="button" onclick="addGroupToGroup('${groupId}')" 
                        class="px-2 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition-colors">
                    <i class="fas fa-layer-group mr-1"></i>Group
                </button>
            </div>
        </div>
    `;
    
    groupContainer.insertAdjacentHTML('beforeend', groupHtml);
    
    if (group.conditions && group.conditions.length > 0) {
        const nestedContainer = groupContainer.querySelector(`[data-id="${groupId}"] .group-conditions`);
        nestedContainer.innerHTML = '';
        group.conditions.forEach(condition => {
            if (condition.field) {
                addConditionToGroup(groupId, condition);
            } else if (condition.operator) {
                addGroupToGroup(groupId, condition);
            }
        });
    }
    
    updateCriteriaJSON();
}

/**
 * Remove a condition
 */
function removeCondition(conditionId) {
    const condition = document.querySelector(`[data-id="${conditionId}"]`);
    if (condition) {
        condition.remove();
        updateCriteriaJSON();
    }
}

/**
 * Remove a group
 */
function removeGroup(groupId) {
    const group = document.querySelector(`[data-id="${groupId}"]`);
    if (group) {
        group.remove();
        updateCriteriaJSON();
    }
}

/**
 * Update condition field and refresh value input
 */
function updateConditionField(conditionId, fieldValue) {
    const condition = document.querySelector(`[data-id="${conditionId}"]`);
    if (!condition) return;
    
    const operatorSelect = condition.querySelector('.condition-operator');
    const operator = operatorSelect ? operatorSelect.value : '';
    const valueContainer = condition.querySelector('.condition-value-container');
    
    if (valueContainer) {
        const currentValue = valueContainer.querySelector('input, select, textarea')?.value || '';
        valueContainer.innerHTML = getValueInput(fieldValue, operator, currentValue);
    }
    
    updateCriteriaJSON();
}

/**
 * Update condition operator and refresh value input
 */
function updateConditionOperator(conditionId, operatorValue) {
    const condition = document.querySelector(`[data-id="${conditionId}"]`);
    if (!condition) return;
    
    const fieldSelect = condition.querySelector('.condition-field');
    const field = fieldSelect ? fieldSelect.value : '';
    const valueContainer = condition.querySelector('.condition-value-container');
    
    if (valueContainer) {
        const currentValue = valueContainer.querySelector('input, select, textarea')?.value || '';
        valueContainer.innerHTML = getValueInput(field, operatorValue, currentValue);
    }
    
    updateCriteriaJSON();
}

/**
 * Get value input based on field type and operator
 */
function getValueInput(field, operator, currentValue) {
    if (!field || !operator) {
        return '<input type="text" class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter value" onchange="updateCriteriaJSON()">';
    }
    
    const fieldConfig = AVAILABLE_FIELDS[field];
    if (!fieldConfig) {
        return '<input type="text" class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="' + (currentValue || '') + '" onchange="updateCriteriaJSON()">';
    }
    
    // Handle 'in' and 'not_in' operators - need multiple values
    if (operator === 'in' || operator === 'not_in') {
        const values = Array.isArray(currentValue) ? currentValue : (currentValue ? [currentValue] : []);
        return `
            <div class="multi-value-input">
                <div class="values-list space-y-1 mb-2">
                    ${values.map((val, idx) => `
                        <div class="flex gap-1">
                            <input type="text" class="condition-value-item flex-1 border border-gray-300 rounded-md px-2 py-1 text-sm" 
                                   value="${val}" onchange="updateCriteriaJSON()">
                            <button type="button" onclick="removeValueItem(this)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">×</button>
                        </div>
                    `).join('')}
                </div>
                <button type="button" onclick="addValueItem(this)" class="px-2 py-1 bg-blue-600 text-white rounded text-xs">
                    <i class="fas fa-plus mr-1"></i>Add Value
                </button>
            </div>
        `;
    }
    
    // Handle boolean fields
    if (fieldConfig.type === 'boolean') {
        return `
            <select class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateCriteriaJSON()">
                <option value="true" ${currentValue === true || currentValue === 'true' || currentValue === '1' ? 'selected' : ''}>True</option>
                <option value="false" ${currentValue === false || currentValue === 'false' || currentValue === '0' ? 'selected' : ''}>False</option>
            </select>
        `;
    }
    
    // Handle select fields
    if (fieldConfig.type === 'select' && fieldConfig.options) {
        return `
            <select class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updateCriteriaJSON()">
                <option value="">Select Value</option>
                ${fieldConfig.options.map(opt => 
                    `<option value="${opt}" ${currentValue === opt ? 'selected' : ''}>${opt}</option>`
                ).join('')}
            </select>
        `;
    }
    
    // Handle number fields
    if (fieldConfig.type === 'number') {
        return `<input type="number" class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="${currentValue || ''}" onchange="updateCriteriaJSON()">`;
    }
    
    // Default: text input
    return `<input type="text" class="condition-value w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" value="${currentValue || ''}" onchange="updateCriteriaJSON()">`;
}

/**
 * Add value item for 'in' and 'not_in' operators
 */
function addValueItem(button) {
    const container = button.closest('.multi-value-input').querySelector('.values-list');
    const newItem = document.createElement('div');
    newItem.className = 'flex gap-1';
    newItem.innerHTML = `
        <input type="text" class="condition-value-item flex-1 border border-gray-300 rounded-md px-2 py-1 text-sm" 
               onchange="updateCriteriaJSON()">
        <button type="button" onclick="removeValueItem(this)" class="px-2 py-1 bg-red-600 text-white rounded text-xs">×</button>
    `;
    container.appendChild(newItem);
    updateCriteriaJSON();
}

/**
 * Remove value item
 */
function removeValueItem(button) {
    button.closest('.flex').remove();
    updateCriteriaJSON();
}

/**
 * Update the hidden criteria JSON input
 */
function updateCriteriaJSON() {
    const rootOperator = document.getElementById('root-operator');
    const container = document.getElementById('conditions-container');
    const criteriaInput = document.getElementById('criteria-json');
    
    if (!container || !criteriaInput) return;
    
    const conditions = [];
    
    // Process root level conditions (those without a parent group or with empty parent group)
    container.querySelectorAll('.condition-item, .group-item').forEach(element => {
        const parentGroup = element.getAttribute('data-parent-group');
        // Only process root-level items (no parent group or empty string)
        if (!parentGroup || parentGroup === '') {
            if (element.classList.contains('condition-item')) {
                const condition = extractCondition(element);
                if (condition) conditions.push(condition);
            } else if (element.classList.contains('group-item')) {
                const group = extractGroup(element);
                if (group) conditions.push(group);
            }
        }
    });
    
    const criteria = {
        operator: rootOperator ? rootOperator.value : 'AND',
        conditions: conditions
    };
    
    criteriaInput.value = JSON.stringify(criteria);
}

/**
 * Extract condition data from element
 */
function extractCondition(element) {
    const field = element.querySelector('.condition-field')?.value;
    const operator = element.querySelector('.condition-operator')?.value;
    const valueContainer = element.querySelector('.condition-value-container');
    
    if (!field || !operator) return null;
    
    let value;
    const valueInput = valueContainer?.querySelector('.condition-value, .condition-value-item');
    
    if (valueInput) {
        // Handle multi-value inputs (for 'in' and 'not_in')
        if (operator === 'in' || operator === 'not_in') {
            const items = valueContainer.querySelectorAll('.condition-value-item');
            value = Array.from(items).map(item => item.value).filter(v => v);
        } else {
            value = valueInput.value;
        }
    }
    
    // Convert value types
    if (value !== undefined && value !== null && value !== '') {
        const fieldConfig = AVAILABLE_FIELDS[field];
        if (fieldConfig) {
            if (fieldConfig.type === 'number' && !Array.isArray(value)) {
                value = parseFloat(value);
            } else if (fieldConfig.type === 'boolean' && !Array.isArray(value)) {
                value = value === 'true' || value === true || value === '1';
            }
        }
    }
    
    return { field, operator, value };
}

/**
 * Extract group data from element
 */
function extractGroup(element) {
    const operator = element.querySelector('.group-operator')?.value;
    const groupContainer = element.querySelector('.group-conditions');
    
    if (!operator || !groupContainer) return null;
    
    const conditions = [];
    
    groupContainer.querySelectorAll('.condition-item, .group-item').forEach(childElement => {
        if (childElement.classList.contains('condition-item')) {
            const condition = extractCondition(childElement);
            if (condition) conditions.push(condition);
        } else if (childElement.classList.contains('group-item')) {
            const group = extractGroup(childElement);
            if (group) conditions.push(group);
        }
    });
    
    return { operator, conditions };
}

// Update criteria JSON on form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('programForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            updateCriteriaJSON();
            const criteriaInput = document.getElementById('criteria-json');
            if (!criteriaInput || !criteriaInput.value || criteriaInput.value === '{}') {
                e.preventDefault();
                alert('Please add at least one condition to the criteria.');
                return false;
            }
        });
    }
});

