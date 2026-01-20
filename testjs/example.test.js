// Simple test example
test('adds 1 + 2 to equal 3', () => {
  expect(1 + 2).toBe(3);
});

// Example test for a simple function
function add(a, b) {
  return a + b;
}

test('adds two numbers correctly', () => {
  expect(add(2, 3)).toBe(5);
});