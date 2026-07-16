# Contributing to Umar Bakery

First off, thank you for considering contributing to Umar Bakery! It's people like you that make Umar Bakery such a great tool.

## 1. Where do I go from here?

If you've noticed a bug or have a feature request, make sure to check our [Issues](../../issues) to see if someone else in the community has already created a ticket. If not, go ahead and make one!

## 2. Fork & create a branch

If this is something you think you can fix, then fork Umar Bakery and create a branch with a descriptive name.

A good branch name would be (where issue #325 is the ticket you're working on):

```sh
git checkout -b 325-add-admin-sales-report
```

## 3. Implement your fix or feature

At this point, you're ready to make your changes. Feel free to ask for help; everyone is a beginner at first. 

**Important Architectural Guidelines:**
*   **Do NOT put business logic in Controllers.** Controllers should only handle request validation and return responses.
*   **Use Services.** All core logic should reside in classes within `app/Services/`.
*   **Write Tests.** Ensure your feature is covered by PHPUnit Feature or Unit tests. We aim for high test coverage on all critical paths (Checkout, Payment, Cart).

## 4. Make a Pull Request

At this point, you should switch back to your master branch and make sure it's up to date with Umar Bakery's master branch:

```sh
git remote add upstream https://github.com/yourusername/umar-bakery.git
git checkout master
git pull upstream master
```

Then update your feature branch from your local copy of master, and push it!

```sh
git checkout 325-add-admin-sales-report
git rebase master
git push --set-upstream origin 325-add-admin-sales-report
```

Finally, go to GitHub and make a Pull Request.

## 5. Code Review

Once your PR is submitted, our team will review it. We may ask for changes. Please be open to feedback; it's all to ensure the highest quality of code!
