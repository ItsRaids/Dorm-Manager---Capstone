# DormHub

DormHub is a household and roommate management application designed to simplify shared living. It provides a centralized platform for managing households, assigning chores, organizing shopping lists, scheduling events, communicating with roommates, voting on decisions, and tracking household activities.

The application is being developed as part of the **CPSC 4910 Capstone Project**.

## Technology Stack

- **Backend:** Python, FastAPI
- **Database:** MySQL, SQLAlchemy
- **Frontend:** JavaScript, HTML, CSS
- **Prototype Storage:** Browser localStorage
- **Version Control:** GitHub

**Current Development Status:** Weeks 1 through 3 have been implemented in the browser-based prototype. Backend integration and shared multiuser functionality remain in development.

## Project Progress

| Week | Development Phase | Status |
|---|---|---|
| Week 1 | Foundation & Household Setup | Frontend Complete |
| Week 2 | Household Management | Frontend Complete |
| Week 3 | Communication & Decision Making | Frontend Complete |
| Week 4 | Money, Documents & Rewards | In Progress |
| Week 5 | Testing, Polish & Final Release | Planned |

---

## Week 1: Foundation & Household Setup

**Goal:** Establish the application's foundation and allow users to create accounts, manage profiles, and organize households.

### User Accounts

- [x] Create account
- [x] Login / logout
- [x] User profile
- [x] Password management

### Households

- [x] Create a household/group
- [x] Join a household
- [x] Generate invite links
- [x] Invite roommates through the prototype interface
- [x] View household members

### Application Setup

- [x] Initial application structure
- [x] Main dashboard
- [x] Sidebar navigation
- [x] Basic frontend permissions
- [x] Redesigned user interface
- [x] Browser-based data persistence
- [ ] Integrate frontend with FastAPI
- [ ] Connect application to MySQL
- [ ] Implement secure server-side authentication and permissions

### Sprint Checkpoint

- [x] Users can register and log in within the prototype
- [x] Users can create or join households
- [x] Household invitation functionality implemented in the prototype
- [x] Household members appear on the dashboard
- [x] Redesigned dashboard and login interface implemented

**Sprint Status:** Frontend Complete

The foundation of DormHub has been established with a redesigned interface, user account management, household creation, and navigation.

The original login and household pages have been removed in favor of the remodeled versions. The application now uses a consistent dark-themed interface.

---

## Week 2: Household Management

**Goal:** Implement essential tools for organizing household responsibilities, shared resources, and events.

### Chores

- [x] Create chores
- [x] Assign chores to household members
- [x] Mark chores complete
- [x] Edit chores
- [x] Delete chores
- [x] Display assigned chores on the dashboard

### Shopping & Household Items

- [x] Shared shopping list interface
- [x] Add shopping items
- [x] Check off purchased items
- [x] Household/move-in items list
- [x] Mark items that someone is bringing
- [x] Manage household items

### Calendar

- [x] Add events
- [x] Edit events
- [x] Delete events
- [x] Display upcoming events
- [x] Show household events on the dashboard

### Household Information

- [x] Contact list with names, phone numbers, and emails
- [x] Household/dorm rules
- [x] Household announcements
- [x] Display announcements on the dashboard

### Sprint Checkpoint

- [x] Members can manage chores within the prototype
- [x] Shopping and household lists are available through the household interface
- [x] Calendar event management is functional
- [x] Contacts and announcements are accessible through the application
- [ ] Synchronize household data between multiple devices

**Sprint Status:** Frontend Complete

The household management tools have been implemented and incorporated into the redesigned interface. Users can organize responsibilities, manage shopping and household items, and schedule events.

All data currently persists locally within the browser. Synchronization between household members will be implemented during backend integration.

---

## Week 3: Communication & Decision Making

**Goal:** Provide household members with tools to communicate, make decisions, and distribute responsibilities.

### Messaging

- [x] Household group chat interface
- [x] Direct messages
- [x] Message history
- [x] Message storage within the prototype
- [ ] Real-time messaging between devices
- [ ] Backend message persistence

### Voting

- [x] Create polls
- [x] Add voting options
- [x] Cast votes
- [x] Display voting results
- [x] Store poll results locally
- [ ] Enforce voting permissions through the backend

### Anonymous Suggestions

- [x] Submit anonymous suggestions
- [x] Household suggestion board
- [x] View submitted suggestions
- [x] Hide suggestion authors in the interface
- [ ] Implement server-side anonymity protections

### Chore Wheel

- [x] Select household members
- [x] Randomly select members
- [x] Randomly distribute unfinished chores
- [x] Update chore assignments

### Shared Wishlist

- [x] Add Amazon wishlist links
- [x] Edit wishlist links
- [x] Remove wishlist links
- [x] Open shared wishlists
- [x] Access wishlist functionality through household navigation

### Sprint Checkpoint

- [x] Group chat works within the browser prototype
- [x] Direct messaging functionality implemented
- [x] Voting system implemented
- [x] Anonymous suggestions can be submitted through the interface
- [x] Chore wheel randomly assigns chores
- [x] Wishlist management implemented
- [ ] Enable communication and voting across separate user devices

**Sprint Status:** Frontend Complete

The third sprint expanded DormHub beyond household organization by introducing communication and decision-making tools.

Group messaging, direct messages, voting, anonymous suggestions, randomized chore assignments, and shared wishlists have been added.

These features are currently functional within the browser prototype and will require backend integration for shared household access.

---

## Week 4: Money, Documents & Rewards

**Goal:** Expand the application with financial management, document storage, rewards, and notifications.

### Expenses & Budget

- [ ] Add shared expenses
- [ ] Record who made a purchase
- [ ] Split expenses between members
- [ ] Household budget
- [ ] Track spending
- [ ] Purchase history

### Documents

- [ ] Upload lease agreements
- [ ] Upload household/dorm rules
- [ ] Upload move-out documents
- [ ] View/download household documents
- [ ] Delete or replace documents

### Points & Levels

- [ ] Award points for completed chores
- [ ] Award points for household contributions
- [ ] Track individual member points
- [ ] Implement member levels
- [x] Design household leaderboard interface
- [x] Design dashboard level and XP display
- [ ] Connect points and levels to completed activities

### Notifications

- [ ] Chore reminders
- [ ] Calendar reminders
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Push notifications where supported

### Sprint Checkpoint

- [ ] Expenses are recorded and tracked
- [ ] Household budget is functional
- [ ] Documents can be securely stored
- [ ] Points are awarded correctly
- [ ] Notifications are connected to chores and calendar events

**Sprint Status:** In Progress

The redesigned dashboard includes visual elements for household levels, experience points, and a roommate leaderboard.

The underlying rewards system, expense tracking, document management, and notifications remain development priorities.

---

## Week 5: Testing, Polish & Final Release

**Goal:** Integrate all features into a stable application and prepare the final capstone demonstration.

### Backend Integration

- [ ] Connect frontend to FastAPI endpoints
- [ ] Integrate MySQL database
- [ ] Implement SQLAlchemy models
- [ ] Replace localStorage with persistent backend storage
- [ ] Synchronize household data across users
- [ ] Implement secure account authentication
- [ ] Connect messaging, voting, and household management to the backend

### Testing

- [ ] User account testing
- [ ] Household/invite testing
- [ ] Messaging testing
- [ ] Calendar testing
- [ ] Chore system testing
- [ ] Expense testing
- [ ] Document upload testing
- [ ] Voting testing
- [ ] Notification testing
- [ ] Multiuser integration testing

### Security

- [ ] Household access permissions
- [ ] Private message security
- [ ] Document access restrictions
- [ ] Secure user authentication
- [ ] Input validation
- [ ] Server-side authorization

### Application Polish

- [ ] Mobile responsiveness testing
- [x] Consistent dark-themed interface
- [x] Redesigned dashboard
- [x] Updated sidebar navigation
- [ ] Improve error messages
- [ ] Add loading states
- [ ] Improve empty-page states
- [ ] Accessibility testing

### Finalization

- [ ] Resolve outstanding bugs
- [ ] Clean and validate database
- [ ] Optimize application performance
- [ ] Deploy final build
- [x] Update GitHub README
- [ ] Create user instructions
- [ ] Prepare final presentation
- [ ] Prepare live demonstration

**Sprint Status:** Planned

---

## Current Prototype

The current DormHub prototype includes a redesigned dashboard inspired by modern household management applications.

### Dashboard Features

- Dark purple user interface
- Sidebar navigation
- Household selection and member display
- Personalized welcome screen
- Household level and XP display
- Roommate leaderboard
- Assigned chore list
- Upcoming household events
- Household announcements
- Access to communication and management features

### Running the Prototype

1. Download or clone the repository.
2. Navigate to the `frontend` directory.
3. Open `index.html` in a web browser.
4. Sign in using the demonstration account.
5. Explore the household management features.

**Demo Credentials**

Email: `dean@dormhub.demo`

Password: `demo1234`

### Important Development Notes

The current prototype uses browser localStorage to save application data.

This allows frontend functionality to be demonstrated without requiring the backend server or database to be running.

However, localStorage is limited to the user's browser and does not provide real-time synchronization between different devices.

The final application is intended to use Python FastAPI, MySQL, and SQLAlchemy to provide persistent shared household data, authentication, and secure communication.

The demo credentials are intended only for the local prototype.

---

## Final Checkpoint: Full Feature Set

The following checklist reflects the current development status.

- [x] Accounts (Frontend Prototype)
- [x] Households & Invites (Frontend Prototype)
- [x] Messaging & Group Chat (Frontend Prototype)
- [x] Calendar (Frontend Prototype)
- [x] Chores & Chore Wheel (Frontend Prototype)
- [x] Shopping & Household Items (Frontend Prototype)
- [ ] Notifications
- [x] Shared Wishlist (Frontend Prototype)
- [ ] Expenses & Budget
- [x] Voting (Frontend Prototype)
- [ ] Document Management
- [ ] Functional Points & Levels System
- [x] Anonymous Suggestions (Frontend Prototype)
- [x] Announcements (Frontend Prototype)
- [ ] Backend Integration
- [ ] Multiuser Synchronization
- [ ] Final Testing & Deployment

## Project Team

**CPSC 4910 Capstone: Team 1**

| Team Member | Role |
|---|---|
| Paul Elswick | Project Manager |
| Linda Huynh | Scrum Master |
| Dean Holsomback | Developer |
| Lun Mayes | Developer |
| Samuel Whitlock | Developer |

## Future Development

The next major development objective is connecting the completed frontend features to the FastAPI backend and MySQL database.

Once integrated, DormHub will support persistent household information, communication between roommates on separate devices, secure user accounts, and shared household management.

The project will then move into final testing, optimization, deployment, and presentation preparation.
