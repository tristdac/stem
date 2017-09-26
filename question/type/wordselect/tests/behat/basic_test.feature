@core @qtype @qtype_wordselect @_switch_window
Feature: Test all the basic functionality of this question type
  In order to evaluate students responses, As a teacher I need to
  create and preview wordselect (Select correct words) questions.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Create, edit then preview a wordselect question.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

    # Create a new question.
    And I add a "Word Select" question filling the form with:
      | Question name             | Word-Select-001                   |
      | Introduction              | Select the verbs in the following text  |
      | Question text             | The cat [sat] and the cow [jumped]  |
      | General feedback          | This is general feedback       |
      | Hint 1                    | First hint                    |
      | Hint 2                    | Second hint                   |
    Then I should see "Word-Select-001"

    # Preview it.
    When I click on "Preview" "link" in the "Word-Select-001" "table_row"
    And I switch to "questionpreview" window

    #################################################
    #Interactive with multiple triddes
    #################################################
    And I set the following fields to these values:
      | How questions behave | Interactive with multiple tries |
      | Marked out of        | 2                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown |
      | Right answer         | Shown |
    And I press "Start again with these options"

    #Select all (both) correct options
    And I click on "sat" "text" 
    And I click on "jumped" "text"
    And I press "Check"      
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    
    #Select one incorrect option on the first attempt
    #and all/both correct options on the second attempt
    ################################################
    #first attempt
    And I press "Start again with these options"
    And I click on "sat" "text" 
    And I press "Check"      
    And I should see "Your answer is partially correct."

    ################################################
    #second attempt
    And I press "Try again"
    #sat should remain selected so no need to select again
    And I click on "jumped" "text"
    And I press "Check"      
    And I should see "Your answer is correct."
    And I should see "Mark 1.67 out of 2.00"
    

    ##################################################
    # Immediate Feedback behaviour
     And I set the following fields to these values:
      | How questions behave | Immediate feedback |
      | Marked out of        | 2                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown |
      | Right answer         | Shown |
    
    And I press "Start again with these options" 
    And I click on "sat" "text" 
    And I click on "jumped" "text"
    And I press "Check"      
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    And I wait "2" seconds
    
    And I press "Start again with these options" 
    And I click on "sat" "text" 
    And I press "Check"      
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "2" seconds
    

    ##################################################
    # Deferred Feedback behaviour
     And I set the following fields to these values:
      | How questions behave | Deferred feedback |
      | Marked out of        | 2                               |
      | Marks                | Show mark and max               |
      | Specific feedback    | Shown |
      | Right answer         | Shown |
    
    And I press "Start again with these options" 
    And I click on "sat" "text" 
    And I click on "jumped" "text"
    And I press "Submit and finish"      
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    And I wait "5" seconds

    And I press "Start again with these options" 
    And I click on "sat" "text" 
    And I press "Submit and finish"      
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "5" seconds

    And I press "Start again with these options" 
    And I click on "sat" "text" 
    And I click on "cow" "text"
    And I click on "jumped" "text"

    And I press "Submit"      
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "5" seconds

    #This doesn't work for some reason and needs fixing
    #And I press "Start again with these options" 
    #And I click on "cat" "text" 
    #And I click on "mat" "text"
    #And I click on "cow" "text"
  
    #And I press "Submit and finish"      
    #And I should see "Your answer is incorrect."
    #And I should see "Mark 0.00 out of 2.00"
    #And I wait "5" seconds
